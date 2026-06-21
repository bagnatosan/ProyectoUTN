<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreReservationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    private const VALID_STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    public function __construct(
        private AvailabilityService $availabilityService,
    ) {}

    public function create(Request $request)
    {
        $products = Product::where('is_active', true)->get();

        $selectedProduct = null;
        if ($request->has('product_id')) {
            $selectedProduct = Product::where('is_active', true)
                ->find($request->input('product_id'));
        }

        return view('reservations.create', compact('products', 'selectedProduct'));
    }

    public function store(StoreReservationRequest $request)
    {
        $product = Product::where('is_active', true)->find($request->product_id);

        if (!$product) {
            return back()->withInput()
                ->withErrors(['product_id' => 'El producto seleccionado no está disponible.']);
        }

        $businessProfileId = $product->business_profile_id;

        $reservation = DB::transaction(function () use ($request, $product, $businessProfileId) {
            $isAvailable = $this->availabilityService->isSlotAvailable(
                $businessProfileId,
                $request->reservation_date,
                $request->reservation_time,
            );

            if (!$isAvailable) {
                DB::rollBack();
                return back()->withInput()
                    ->withErrors(['reservation_time' => 'Este horario ya no está disponible. Por favor elegí otro.']);
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
            return $reservation;
        }

        return redirect()->route('reservations.create')
            ->with('success', 'Reserva solicitada con éxito. Te confirmaremos pronto el turno para el '
                . $request->reservation_date . ' a las ' . $request->reservation_time . '.');
    }

    public function clientHistory()
    {
        if (Auth::user()->role !== 'client') {
            abort(403, 'Esta sección es solo para clientes.');
        }

        $reservations = Reservation::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();

        return view('reservations.client_history', compact('reservations'));
    }

    public function manage()
    {
        return view('reservations.manage');
    }

    public function getReservations(Request $request)
    {
        $businessProfile = $request->user()->businessProfile;

        if (!$businessProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Primero debes completar tu perfil de negocio.',
            ], 403);
        }

        $query = Reservation::whereHas('product', function ($q) use ($businessProfile) {
            $q->where('business_profile_id', $businessProfile->id);
        })->with(['product', 'user']);

        $filter = $request->input('filter', 'today');
        $today = Carbon::today();

        switch ($filter) {
            case 'today':
                $query->whereDate('reservation_date', $today);
                break;
            case 'tomorrow':
                $query->whereDate('reservation_date', $today->copy()->addDay());
                break;
            case 'week':
                $query->whereBetween('reservation_date', [
                    $today->copy()->startOfWeek(),
                    $today->copy()->endOfWeek(),
                ]);
                break;
            case 'month':
                $query->whereBetween('reservation_date', [
                    $today->copy()->startOfMonth(),
                    $today->copy()->endOfMonth(),
                ]);
                break;
        }

        $reservations = $query->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $reservations,
        ]);
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $businessProfile = $request->user()->businessProfile;
        $product = $reservation->product()->first();

        if (!$businessProfile || !$product || $product->business_profile_id !== $businessProfile->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tenés permiso para modificar esta reserva.',
                ], 403);
            }
            return back()->with('error', 'No tenés permiso para modificar esta reserva.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::VALID_STATUSES)],
        ], [
            'status.required' => 'El estado es obligatorio.',
            'status.in'       => 'El estado no es válido.',
        ]);

        $reservation->update([
            'status' => $request->status,
        ]);

        $statusLabels = [
            'pending'   => 'Pendiente',
            'confirmed' => 'Confirmada',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        ];

        $message = 'Reserva #' . $reservation->id . ' actualizada a "' . ($statusLabels[$request->status] ?? $request->status) . '".';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);

        if (!$reservation->isCancellable()) {
            return $this->cancelErrorResponse($request, 'Esta reserva no se puede cancelar porque su estado es "' . $reservation->status . '".');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $cancelledBy = Auth::user()->role === 'client' ? 'client' : 'seller';

        DB::transaction(function () use ($reservation, $request, $cancelledBy) {
            $reservation->update([
                'status'             => 'cancelled',
                'cancellation_reason' => $request->reason,
                'cancelled_by'       => $cancelledBy,
            ]);
        });

        $this->sendCancellationNotifications($reservation, $cancelledBy, $request->reason);

        $message = 'Reserva cancelada exitosamente.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
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

    private function cancelErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->with('error', $message);
    }
}
