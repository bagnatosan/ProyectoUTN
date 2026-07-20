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
        $business = null;

        if ($request->has('product_id')) {
            $selectedProduct = Product::where('is_active', true)
                ->find($request->input('product_id'));
        }

        if ($request->has('business_profile_id')) {
            $business = BusinessProfile::find($request->input('business_profile_id'));
        }

        if ($selectedProduct) {
            $products = Product::where('is_active', true)
                ->where('business_profile_id', $selectedProduct->business_profile_id)
                ->with('businessProfile')
                ->get();
            $business = $selectedProduct->businessProfile;
        } elseif ($business) {
            $products = Product::where('is_active', true)
                ->where('business_profile_id', $business->id)
                ->with('businessProfile')
                ->get();
        } else {
            $products = Product::where('is_active', true)
                ->with('businessProfile')
                ->get();
            $business = $products->first()?->businessProfile ?? null;
        }

        // Dirección precargada del cliente
        $clientAddress = null;
        if (Auth::check() && Auth::user()->clientProfile) {
            $clientAddress = Auth::user()->clientProfile->address;
        }

        return view('reservations.create', compact('products', 'selectedProduct', 'business', 'clientAddress'));
    }

    public function store(StoreReservationRequest $request): RedirectResponse|JsonResponse
    {
        $cartItems = null;
        $productsDb = null;
        $product = null;

        if ($request->filled('cart_data')) {
            $cartItems = json_decode($request->cart_data, true);
        }

        if ($cartItems && count($cartItems) > 0) {
            $productIds = collect($cartItems)->pluck('id');
            $productsDb = Product::whereIn('id', $productIds)->with('businessProfile')->get()->keyBy('id');
            
            $firstProduct = $productsDb->first();
            if (!$firstProduct) {
                return back()->withInput()->withErrors(['product_id' => 'Los productos no están disponibles.']);
            }
            $sellerId = $firstProduct->businessProfile->user_id;
            $businessProfile = $firstProduct->businessProfile;
        } else {
            $product = Product::where('is_active', true)->find($request->product_id);
            if (!$product) {
                return back()->withInput()->withErrors(['product_id' => 'El producto seleccionado no está disponible.']);
            }
            $sellerId = $product->businessProfile->user_id;
            $businessProfile = $product->businessProfile;
        }

        $reservation = DB::transaction(function () use ($request, $cartItems, $productsDb, $sellerId, $businessProfile, $product) {
            $isAvailable = $this->availabilityService->isSlotAvailable(
                $sellerId,
                $request->reservation_date,
                $request->reservation_time,
            );

            if (!$isAvailable) {
                return null;
            }

            if ($cartItems) {
                $totalQty = collect($cartItems)->sum('quantity');
                $mainProduct = $productsDb->first();
                
                $reservation = Reservation::create([
                    'product_id'       => $mainProduct->id,
                    'user_id'          => Auth::check() ? Auth::id() : null,
                    'client_name'      => $request->client_name,
                    'client_email'     => $request->client_email,
                    'client_phone'     => $request->client_phone,
                    'quantity'         => $totalQty,
                    'delivery_type'    => $request->delivery_type,
                    'shipping_address' => $request->delivery_type === 'delivery' ? $request->shipping_address : null,
                    'shipping_cost'    => $request->delivery_type === 'delivery' ? ($businessProfile->shipping_cost ?? 0) : 0,
                    'reservation_date' => $request->reservation_date,
                    'reservation_time' => $request->reservation_time,
                    'notes'            => $request->notes,
                    'status'           => 'pending',
                    'payment_status'   => 'pending_upload',
                ]);

                foreach ($cartItems as $item) {
                    $prodDb = $productsDb->get($item['id']);
                    if ($prodDb) {
                        \App\Models\ReservationItem::create([
                            'reservation_id' => $reservation->id,
                            'product_id'     => $prodDb->id,
                            'quantity'       => $item['quantity'],
                            'unit_price'     => $prodDb->price,
                        ]);
                    }
                }
                
                return $reservation;
            } else {
                $reservation = Reservation::create([
                    'product_id'       => $product->id,
                    'user_id'          => Auth::check() ? Auth::id() : null,
                    'client_name'      => $request->client_name,
                    'client_email'     => $request->client_email,
                    'client_phone'     => $request->client_phone,
                    'quantity'         => $request->quantity ?? 1,
                    'delivery_type'    => $request->delivery_type,
                    'shipping_address' => $request->delivery_type === 'delivery' ? $request->shipping_address : null,
                    'shipping_cost'    => $request->delivery_type === 'delivery' ? ($businessProfile->shipping_cost ?? 0) : 0,
                    'reservation_date' => $request->reservation_date,
                    'reservation_time' => $request->reservation_time,
                    'notes'            => $request->notes,
                    'status'           => 'pending',
                    'payment_status'   => 'pending_upload',
                ]);

                \App\Models\ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'product_id'     => $product->id,
                    'quantity'       => $request->quantity ?? 1,
                    'unit_price'     => $product->price,
                ]);

                return $reservation;
            }
        });

        if (!$reservation) {
            return back()->withInput()
                ->withErrors(['reservation_time' => 'Este horario ya no está disponible. Por favor elegí otro.']);
        }

        if ($cartItems) {
            session()->flash('clear_cart_id', $businessProfile->id);
        }

        return redirect()->route('reservations.payment', $reservation->id);
    }

    public function index(): View
    {
        $this->authorize('viewAnyClient', Reservation::class);

        $products = Product::whereHas('reservations', function ($q) {
            $q->where('user_id', Auth::id());
        })->orderBy('name')->get();

        return view('reservations.index', compact('products'));
    }

    public function myReservationsData(Request $request): JsonResponse
    {
        $this->authorize('viewAnyClient', Reservation::class);

        $query = Reservation::forClient()
            ->with(['product.businessProfile']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('product.businessProfile', fn ($bq) => $bq->where('business_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('quick_filter')) {
            $today = Carbon::today();
            switch ($request->quick_filter) {
                case 'today':
                    $query->whereDate('reservation_date', $today);
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
                case 'next_7_days':
                    $query->whereBetween('reservation_date', [$today, $today->copy()->addDays(7)]);
                    break;
                case 'next_30_days':
                    $query->whereBetween('reservation_date', [$today, $today->copy()->addDays(30)]);
                    break;
                case 'upcoming':
                    $query->where('reservation_date', '>=', $today);
                    break;
                case 'past':
                    $query->where('reservation_date', '<', $today);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('reservation_scope')) {
            $today = Carbon::today();
            switch ($request->reservation_scope) {
                case 'upcoming':
                    $query->where('reservation_date', '>=', $today);
                    break;
                case 'past':
                    $query->where('reservation_date', '<', $today);
                    break;
                case 'active':
                    $query->whereIn('status', ['pending', 'confirmed']);
                    break;
                case 'closed':
                    $query->whereIn('status', ['completed', 'cancelled']);
                    break;
            }
        }

        if ($request->has('has_notes') && $request->has_notes !== '') {
            if (filter_var($request->has_notes, FILTER_VALIDATE_BOOLEAN)) {
                $query->whereNotNull('notes')->where('notes', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('notes')->orWhere('notes', '');
                });
            }
        }

        $sortField = 'reservation_date';
        $sortDir = 'desc';
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'date_asc':
                    $sortField = 'reservation_date'; $sortDir = 'asc';
                    break;
                case 'date_desc':
                    $sortField = 'reservation_date'; $sortDir = 'desc';
                    break;
                case 'created_desc':
                    $sortField = 'created_at'; $sortDir = 'desc';
                    break;
                case 'created_asc':
                    $sortField = 'created_at'; $sortDir = 'asc';
                    break;
            }
        }

        $perPage = $request->integer('per_page', 20);
        $reservations = $query->orderBy($sortField, $sortDir)
            ->orderBy('reservation_time')
            ->paginate($perPage);

        $formatted = collect($reservations->items())->map(function ($r) {
            $product = $r->product;
            $businessProfile = $product?->businessProfile;

            return [
                'id'                => $r->id,
                'product_id'        => $r->product_id,
                'quantity'          => $r->quantity,
                'reservation_date'  => $r->reservation_date instanceof \Carbon\Carbon
                    ? $r->reservation_date->format('Y-m-d')
                    : $r->reservation_date,
                'reservation_time'  => substr($r->reservation_time, 0, 5),
                'notes'             => $r->notes,
                'status'               => $r->status,
                'payment_status'       => $r->payment_status,
                'payment_confirmed_at' => $r->payment_confirmed_at?->format('d/m/Y H:i'),
                'cancellation_reason'  => $r->cancellation_reason,
                'can_cancel'           => $r->isCancellable(),
                'was_modified'      => $r->updated_at && $r->created_at
                    && $r->updated_at->diffInMinutes($r->created_at) > 1,
                'created_at'        => $r->created_at->format('Y-m-d H:i'),
                'product'           => $product ? [
                    'name'          => $product->name,
                    'image'         => $product->image ? storage_url($product->image) : null,
                    'price'         => (float) $product->price,
                    'business_name' => $businessProfile?->business_name ?? 'Emprendedor',
                ] : [
                    'name'          => 'Producto eliminado',
                    'image'         => null,
                    'price'         => 0,
                    'business_name' => 'Emprendedor',
                ],
            ];
        });

        return response()->json([
            'success'       => true,
            'data'          => $formatted,
            'total'         => $reservations->total(),
            'current_page'  => $reservations->currentPage(),
            'last_page'     => $reservations->lastPage(),
            'per_page'      => $reservations->perPage(),
        ]);
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
            ->with(['product', 'user', 'items.product']);

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

        $formatted = collect($reservations->items())->map(function ($r) {
            $product = $r->product;
            return array_merge($r->toArray(), [
                'product' => $product ? [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'image' => $product->image ? storage_url($product->image) : null,
                    'price' => (float) $product->price,
                ] : null,
                'items' => $r->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product ? $item->product->name : 'Producto eliminado',
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                    ];
                })->toArray(),
            ]);
        });

        return response()->json([
            'success'       => true,
            'data'          => $formatted,
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
            $reservationDateTime = \Carbon\Carbon::parse(
                $reservation->reservation_date->format('Y-m-d') . ' ' . $reservation->reservation_time
            );
            if ($reservationDateTime->isFuture()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No podés completar una reserva antes de su fecha y hora pactada.',
                ], 422);
            }
            $updateData['completed_at'] = now();
        }

        if ($newStatus === 'cancelled') {
            $updateData['cancellation_reason'] = $request->cancellation_reason;
            $updateData['cancelled_by'] = $request->user()->id;
        }

        DB::transaction(function () use ($reservation, $updateData, $newStatus, $request) {
            $reservation->update($updateData);

            if (!$reservation->user) {
                return;
            }

            if ($newStatus === 'confirmed') {
                $reservation->user->notify(new \App\Notifications\ReservationConfirmed($reservation->fresh()));
            } elseif ($newStatus === 'completed') {
                $reservation->user->notify(new \App\Notifications\ReservationCompleted($reservation->fresh()));
            } elseif ($newStatus === 'cancelled') {
                $reservation->user->notify(new \App\Notifications\ReservationCancelled($reservation, 'seller', $request->cancellation_reason ?? null));
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

        $reservation->load(['product.businessProfile', 'items.product']);

        $preferenceId = null;
        $initUrl = null;
        $business = $reservation->product->businessProfile;
        
        if (!empty($business->mp_access_token)) {
            $items = [];
            if ($reservation->items->isNotEmpty()) {
                foreach ($reservation->items as $item) {
                    $items[] = [
                        'title' => $item->product->name,
                        'quantity' => (int)$item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'currency_id' => 'ARS',
                    ];
                }
            } else {
                $items[] = [
                    'title' => $reservation->product->name,
                    'quantity' => (int)$reservation->quantity,
                    'unit_price' => (float)$reservation->product->price,
                    'currency_id' => 'ARS',
                ];
            }

            if ($reservation->delivery_type === 'delivery' && $reservation->shipping_cost > 0) {
                $items[] = [
                    'title' => 'Costo de envío',
                    'quantity' => 1,
                    'unit_price' => (float)$reservation->shipping_cost,
                    'currency_id' => 'ARS',
                ];
            }

            try {
                $mpToken = $business->mp_access_token;
                if (str_starts_with($mpToken, 'APP_USR-MOCK-')) {
                    $preferenceId = 'mock_pref_' . $reservation->id;
                    $reservation->mp_preference_id = $preferenceId;
                    $reservation->save();
                } else {
                    $webhookUrl = route('reservations.mercadopago.webhook');
                    $host = parse_url($webhookUrl, PHP_URL_HOST);
                    $payload = [
                        'items' => $items,
                        'external_reference' => (string)$reservation->id,
                        'payer' => [
                            'name' => $reservation->user ? $reservation->user->name : 'Comprador',
                            'email' => $reservation->user ? $reservation->user->email : 'test_user_123456@testuser.com',
                        ],
                        'back_urls' => [
                            'success' => route('reservations.index'),
                            'pending' => route('reservations.index'),
                            'failure' => route('reservations.index'),
                        ],
                    ];
                    
                    // Only pass notification_url and auto_return redirects if it is a public HTTPS domain (prevents MP 400 Bad Request error on local env)
                    if ($host && $host !== 'localhost' && $host !== '127.0.0.1' && !str_starts_with($host, '192.168.') && !str_starts_with($host, '10.')) {
                        $payload['notification_url'] = $webhookUrl;
                        $payload['auto_return'] = 'approved';
                    }

                    $response = \Illuminate\Support\Facades\Http::withToken($mpToken)
                        ->post('https://api.mercadopago.com/checkout/preferences', $payload);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        $preferenceId = $responseData['id'];
                        $initUrl = $responseData['init_point'] ?? $responseData['sandbox_init_point'] ?? null;
                        $reservation->mp_preference_id = $preferenceId;
                        $reservation->save();
                    }
                }
            } catch (\Exception $e) {
                // Fail silently and allow bank transfer
            }
        }

        return view('reservations.payment', compact('reservation', 'preferenceId', 'initUrl'));
    }

    public function mercadopagoWebhook(Request $request): JsonResponse
    {
        $paymentId = $request->input('data.id') ?? $request->input('id');
        $topic = $request->input('type') ?? $request->input('topic');

        if ($topic === 'payment' && $paymentId) {
            $reservations = Reservation::where('payment_status', 'pending_upload')
                ->whereNotNull('mp_preference_id')
                ->with('product.businessProfile')
                ->latest()
                ->take(15)
                ->get();

            foreach ($reservations as $res) {
                $mpToken = $res->product->businessProfile->mp_access_token;
                if (!$mpToken) continue;

                try {
                    $response = \Illuminate\Support\Facades\Http::withToken($mpToken)
                        ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

                    if ($response->successful()) {
                        $paymentData = $response->json();
                        if (isset($paymentData['external_reference']) && (int)$paymentData['external_reference'] === $res->id) {
                            $status = $paymentData['status'];
                            $res->mp_payment_id = (string)$paymentId;
                            $res->mp_status = $status;
                            
                            if ($status === 'approved') {
                                $res->status = 'confirmed';
                                $res->payment_status = 'confirmed';
                                $res->payment_confirmed_at = now();
                                $res->transfer_amount = $paymentData['transaction_amount'];
                                $res->transfer_date = now()->format('Y-m-d');
                                $res->transfer_reference = 'MP-' . $paymentId;
                            }
                            $res->save();
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // Fail silently
                }
            }
        }

        return response()->json(['success' => true]);
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

        $path = $request->file('receipt')->store('receipts', 'r2');

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

    public function simulatePaymentSuccess(Reservation $reservation): RedirectResponse
    {
        $business = $reservation->product->businessProfile;
        if (!$business || !str_starts_with($business->mp_access_token ?? '', 'APP_USR-MOCK-')) {
            abort(403, 'La simulación solo está permitida con credenciales de prueba MOCK.');
        }

        $reservation->update([
            'payment_status'      => 'confirmed',
            'payment_confirmed_at' => now(),
            'status'              => 'confirmed',
            'transfer_reference'  => 'MP-MOCK-' . strtoupper(uniqid()),
        ]);

        if ($reservation->user) {
            $reservation->user->notify(new PaymentConfirmed($reservation->fresh()));
        }

        return redirect()->route('reservations.index')
            ->with('success', '¡Simulación de Pago Mercado Pago exitosa! La reserva ha sido confirmada.');
    }
}
