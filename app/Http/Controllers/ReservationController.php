<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationStatusRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService,
    ) {}

    public function create(Request $request): View
    {
        $products = Product::where('is_active', true)
            ->with('businessProfile')
            ->get();

        $selectedProduct = null;
        if ($request->has('product_id')) {
            $selectedProduct = Product::where('is_active', true)
                ->find($request->input('product_id'));
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
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'notes'            => $request->notes,
                'status'           => 'pending',
            ]);
        });

        if (!$reservation) {
            return back()->withInput()
                ->withErrors(['reservation_time' => 'Este horario ya no está disponible. Por favor elegí otro.']);
        }

        return redirect()->route('reservations.create')
            ->with('success', 'Reserva solicitada con éxito. Te confirmaremos pronto el turno para el '
                . $request->reservation_date . ' a las ' . $request->reservation_time . '.');
    }

    public function index(): View
    {
        $this->authorize('viewAnyClient', Reservation::class);

        $reservations = Reservation::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();

        return view('reservations.client_history', compact('reservations'));
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->integer('per_page', 12);
        $reservations = $query->orderBy('reservation_date')->orderBy('reservation_time')
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
}
