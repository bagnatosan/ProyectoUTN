<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Requests\UpdateReservationStatusRequest;
use App\Notifications\ReservationUpdated;
use App\Notifications\PaymentUploaded;
use App\Notifications\PaymentConfirmed;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService,
    ) {}

    public function create(Request $request): View
    {
        $selectedProduct = null;

        if ($request->has('product_id')) {
            $selectedProduct = Product::where('is_active', true)
                ->find($request->input('product_id'));
        }

        // Solo mostrar productos del negocio al que pertenece el producto seleccionado.
        // Si no viene product_id, mostrar todos (acceso directo a /reservations/create).
        if ($selectedProduct) {
            $products = Product::where('is_active', true)
                ->where('business_profile_id', $selectedProduct->business_profile_id)
                ->with('businessProfile')
                ->get();
        } else {
            $products = Product::where('is_active', true)
                ->with('businessProfile')
                ->get();
        }

        return view('reservations.create', compact('products', 'selectedProduct'));
    }

    public function store(StoreReservationRequest $request): RedirectResponse|JsonResponse
    {
        $product = Product::where('is_active', true)->find($request->product_id);

        if (!$product) {
            return back()->withInput()
                ->withErrors(['product_id' => 'El producto seleccionado no está disponible.']);
        }

        $sellerId = $product->businessProfile->user_id;

        $reservation = DB::transaction(function () use ($request, $product, $sellerId) {
            $isAvailable = $this->availabilityService->isSlotAvailable(
                $sellerId,
                $request->reservation_date,
                $request->reservation_time,
            );

            if (!$isAvailable) {
                DB::rollBack();
                return null;
            }

            return Reservation::create([
                'product_id'       => $product->id,
                'user_id'          => Auth::check() ? Auth::id() : null,
                'client_name'      => $request->client_name,
                'client_email'     => $request->client_email,
                'client_phone'     => $request->client_phone,
                'quantity'         => $request->quantity ?? 1,
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'notes'            => $request->notes,
                'status'           => 'pending',
                'payment_status'   => 'pending_upload',
            ]);
        });

        if (!$reservation) {
            return back()->withInput()
                ->withErrors(['reservation_time' => 'Este horario ya no está disponible. Por favor elegí otro.']);
        }

        return redirect()->route('reservations.payment', $reservation->id);
    }

    public function index(): View
    {
        $this->authorize('viewAnyClient', Reservation::class);

        $reservations = Reservation::forClient()
            ->with(['product.businessProfile.user', 'product'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();

        return view('reservations.index', compact('reservations'));
    }

    public function edit(Reservation $reservation): View|RedirectResponse
    {
        $this->authorize('modify', $reservation);

        $minDate = now()->addDays(2)->format('Y-m-d');
        if ($reservation->reservation_date->format('Y-m-d') < $minDate) {
            return redirect()->route('reservations.index')
                ->with('error', 'No podés modificar una reserva con menos de 2 días de anticipación.');
        }

        $sellerId = $reservation->product->businessProfile->user_id;
        $sellerProfile = $reservation->product->businessProfile;

        $products = Product::where('is_active', true)
            ->where('business_profile_id', $sellerProfile->id)
            ->get();

        $reservation->load('product.businessProfile');

        return view('reservations.edit', compact('reservation', 'products', 'sellerId'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('modify', $reservation);

        $oldData = [
            'product_id' => $reservation->product_id,
            'reservation_date' => $reservation->reservation_date->format('Y-m-d'),
            'reservation_time' => $reservation->reservation_time,
            'notes' => $reservation->notes,
        ];

        $updated = DB::transaction(function () use ($request, $reservation) {
            $sellerId = $reservation->product->businessProfile->user_id;

            $isAvailable = $this->availabilityService->isSlotAvailable(
                $sellerId,
                $request->reservation_date,
                $request->reservation_time,
                $reservation->id,
            );

            if (!$isAvailable) {
                return false;
            }

            $reservation->update([
                'product_id'       => $request->product_id,
                'quantity'         => $request->quantity ?? 1,
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'notes'            => $request->notes,
            ]);

            return true;
        });

        if (!$updated) {
            return back()->withInput()
                ->withErrors(['reservation_time' => 'El horario seleccionado ya no está disponible. Por favor elegí otro.']);
        }

        $changes = [];
        $labels = [
            'product_id' => 'Producto',
            'reservation_date' => 'Fecha',
            'reservation_time' => 'Horario',
            'notes' => 'Notas',
        ];

        $newData = [
            'product_id' => $reservation->product_id,
            'reservation_date' => $reservation->reservation_date->format('Y-m-d'),
            'reservation_time' => $reservation->reservation_time,
            'notes' => $reservation->notes,
        ];

        foreach ($newData as $field => $newValue) {
            if ($oldData[$field] != $newValue) {
                $oldDisplay = $field === 'product_id'
                    ? ($reservation->product?->name ?? 'Anterior')
                    : $oldData[$field];
                $newDisplay = $field === 'product_id'
                    ? (\App\Models\Product::find($newValue)?->name ?? $newValue)
                    : $newValue;
                $changes[$field] = ['old' => $oldDisplay, 'new' => $newDisplay];
            }
        }

        if (!empty($changes)) {
            $seller = $reservation->product?->businessProfile?->user;
            if ($seller) {
                $seller->notify(new ReservationUpdated($reservation->fresh(), $changes));
            }
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Reserva modificada con éxito.');
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse|JsonResponse
    {
        $this->authorize('cancel', $reservation);

        if (!$reservation->isCancellable()) {
            return $this->cancelErrorResponse($request,
                'Esta reserva no se puede cancelar porque su estado es "' . $reservation->status . '".'
            );
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($reservation, $request) {
            $reservation->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $request->reason,
                'cancelled_by'        => Auth::id(),
            ]);
        });

        $this->sendCancellationNotifications(
            $reservation,
            Auth::user()->role === 'client' ? 'client' : 'seller',
            $request->reason
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada exitosamente.',
            ]);
        }

        return back()->with('success', 'Reserva cancelada exitosamente.');
    }

    public function show(Request $request, Reservation $reservation): View
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            abort(403, 'Perfil de negocio no encontrado.');
        }

        $belongsToSeller = $reservation->product->business_profile_id === $businessProfile->id;
        if (!$belongsToSeller) {
            abort(403, 'No autorizado.');
        }

        $reservation->load(['product', 'user', 'canceller']);

        return view('reservations.seller.show', compact('reservation'));
    }

    public function manage(): View
    {
        return view('reservations.seller.index');
    }

    public function getReservations(Request $request): JsonResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Primero debes completar tu perfil de negocio.',
            ], 403);
        }

        $query = Reservation::whereHas('product', fn ($q) => $q->where('business_profile_id', $businessProfile->id))
            ->with(['product', 'user']);

        $filter = $request->input('filter', 'today');
        $today = Carbon::today();

        switch ($filter) {
            case 'all':
                break;
            case 'tomorrow':
                $query->whereDate('reservation_date', $today->copy()->addDay());
                break;
            case 'week':
                $query->whereBetween('reservation_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('reservation_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()]);
                break;
            case 'today':
            default:
                $query->whereDate('reservation_date', $today);
                break;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        $sortBy = in_array($request->sort_by, ['reservation_date', 'client_name', 'status', 'reservation_time']) ? $request->sort_by : 'reservation_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $perPage = $request->integer('per_page', 12);
        $reservations = $query->orderBy($sortBy, $sortDir)
            ->orderBy('reservation_time')
            ->paginate($perPage);

        return response()->json([
            'success'       => true,
            'data'          => $reservations->items(),
            'total'         => $reservations->total(),
            'current_page'  => $reservations->currentPage(),
            'last_page'     => $reservations->lastPage(),
            'per_page'      => $reservations->perPage(),
        ]);
    }

    public function updateStatus(UpdateReservationStatusRequest $request, Reservation $reservation): JsonResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            return response()->json(['success' => false, 'message' => 'Perfil de negocio no encontrado.'], 403);
        }

        $belongsToSeller = $reservation->product->business_profile_id === $businessProfile->id;
        if (!$belongsToSeller) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $newStatus = $request->status;
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'completed') {
            $updateData['completed_at'] = now();
        }

        if ($newStatus === 'cancelled') {
            $updateData['cancellation_reason'] = $request->cancellation_reason;
            $updateData['cancelled_by'] = $request->user()->id;
        }

        DB::transaction(function () use ($reservation, $updateData, $newStatus, $request) {
            $reservation->update($updateData);

            if (in_array($newStatus, ['confirmed', 'cancelled'])) {
                $notification = new \App\Notifications\ReservationCancelled($reservation, 'seller', $request->cancellation_reason ?? null);
                if ($reservation->user) {
                    $reservation->user->notify($notification);
                }
            }
        });

        $statusLabels = [
            'pending'   => 'Pendiente',
            'confirmed' => 'Confirmada',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        ];

        return response()->json([
            'success'     => true,
            'message'     => 'Estado actualizado a "' . ($statusLabels[$newStatus] ?? $newStatus) . '".',
            'reservation' => $reservation->fresh()->load(['product', 'user']),
        ]);
    }

    public function updateSellerNotes(Request $request, Reservation $reservation): JsonResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            return response()->json(['success' => false, 'message' => 'Perfil de negocio no encontrado.'], 403);
        }

        $belongsToSeller = $reservation->product->business_profile_id === $businessProfile->id;
        if (!$belongsToSeller) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'seller_notes' => 'nullable|string|max:5000',
        ]);

        $reservation->update([
            'seller_notes' => $request->seller_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nota interna guardada.',
        ]);
    }

    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            abort(403, 'Perfil de negocio no encontrado.');
        }

        $query = Reservation::with('product')
            ->whereHas('product', fn ($q) => $q->where('business_profile_id', $businessProfile->id));

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = in_array($request->sort_by, ['reservation_date', 'client_name', 'status', 'reservation_time']) ? $request->sort_by : 'reservation_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $reservations = $query->orderBy($sortBy, $sortDir)->get();

        $filename = 'reservas_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($reservations) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8
            fputcsv($output, ['ID', 'Cliente', 'Email', 'Teléfono', 'Producto', 'Fecha', 'Hora', 'Estado', 'Notas', 'Nota Interna', 'Creada']);

            foreach ($reservations as $r) {
                fputcsv($output, [
                    $r->id,
                    $r->client_name,
                    $r->client_email,
                    $r->client_phone,
                    $r->product?->name,
                    $r->reservation_date,
                    $r->reservation_time,
                    $r->status,
                    $r->notes,
                    $r->seller_notes,
                    $r->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function pendingCount(Request $request): JsonResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            return response()->json(['count' => 0]);
        }

        $count = Reservation::forBusiness($businessProfile->id)
            ->pending()
            ->count();

        return response()->json(['count' => $count]);
    }

    private function sendCancellationNotifications(Reservation $reservation, string $cancelledBy, ?string $reason): void
    {
        $notification = new \App\Notifications\ReservationCancelled($reservation, $cancelledBy, $reason);

        if ($reservation->user) {
            $reservation->user->notify($notification);
        }

        $seller = $reservation->product?->businessProfile?->user;
        if ($seller && (!$reservation->user || $seller->id !== $reservation->user->id)) {
            $seller->notify($notification);
        }
    }

    private function cancelErrorResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->with('error', $message);
    }

    public function showPayment(Reservation $reservation): View|RedirectResponse
    {
        if (Auth::id() !== $reservation->user_id) {
            abort(403);
        }

        if ($reservation->payment_status !== 'pending_upload') {
            return redirect()->route('reservations.index');
        }

        $reservation->load('product.businessProfile');

        return view('reservations.payment', compact('reservation'));
    }

    public function uploadReceipt(Request $request, Reservation $reservation): RedirectResponse
    {
        if (Auth::id() !== $reservation->user_id) {
            abort(403);
        }

        if ($reservation->payment_status !== 'pending_upload') {
            return redirect()->route('reservations.index')
                ->with('error', 'Este comprobante ya fue enviado.');
        }

        $request->validate([
            'transfer_amount'    => 'required|numeric|min:0.01',
            'transfer_date'      => 'required|date|before_or_equal:today',
            'transfer_reference' => 'nullable|string|max:255',
            'receipt'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'transfer_amount.required'    => 'Ingresá el monto transferido.',
            'transfer_date.required'      => 'Ingresá la fecha de la transferencia.',
            'transfer_date.before_or_equal' => 'La fecha no puede ser futura.',
            'receipt.required'            => 'Adjuntá el comprobante.',
            'receipt.mimes'               => 'El comprobante debe ser JPG, PNG o PDF.',
            'receipt.max'                 => 'El archivo no puede superar los 5 MB.',
        ]);

        $path = $request->file('receipt')->store('receipts', 'public');

        $reservation->update([
            'payment_status'     => 'uploaded',
            'transfer_amount'    => $request->transfer_amount,
            'transfer_date'      => $request->transfer_date,
            'transfer_reference' => $request->transfer_reference,
            'receipt_path'       => $path,
        ]);

        $seller = $reservation->product?->businessProfile?->user;
        if ($seller) {
            $seller->notify(new PaymentUploaded($reservation->fresh()));
        }

        return redirect()->route('reservations.index')
            ->with('success', '¡Comprobante enviado! El emprendedor lo revisará y confirmará tu reserva a la brevedad.');
    }

    public function confirmPayment(Request $request, Reservation $reservation): RedirectResponse|JsonResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            abort(403);
        }

        if ($reservation->product->business_profile_id !== $businessProfile->id) {
            abort(403);
        }

        if ($reservation->payment_status !== 'uploaded') {
            $msg = 'El comprobante no está disponible para confirmar.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $reservation->update([
            'payment_status'      => 'confirmed',
            'payment_confirmed_at' => now(),
            'status'              => 'confirmed',
        ]);

        if ($reservation->user) {
            $reservation->user->notify(new PaymentConfirmed($reservation->fresh()));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pago confirmado. La reserva fue confirmada.',
            ]);
        }

        return redirect()->route('reservations.detail', $reservation->id)
            ->with('success', 'Pago confirmado. La reserva está confirmada.');
    }
}
