<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Product;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Estados válidos para una reserva.
     */
    private const VALID_STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    /**
     * Muestra el formulario público para crear una nueva reserva.
     *
     * Carga solo productos activos. Si se pasa ?product_id=X por query string,
     * precarga ese producto en el formulario.
     */
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

    /**
     * Guarda una nueva reserva en la base de datos.
     *
     * Incluye doble validación de disponibilidad en servidor:
     * 1. Verifica que exista un AvailabilitySlot activo que cubra la hora solicitada.
     * 2. Verifica que no haya otra reserva (no cancelada) en el mismo horario.
     *
     * Si el usuario está autenticado, asocia la reserva a su cuenta.
     */
    public function store(Request $request)
    {
        // ------------------------------------------------------------------
        // Validación de campos del formulario
        // ------------------------------------------------------------------
        $request->validate([
            'product_id'      => 'required|integer|exists:products,id',
            'client_name'     => 'required|string|max:255',
            'client_email'    => 'required|email|max:255',
            'client_phone'    => 'nullable|string|max:50',
            'reservation_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'notes'           => 'nullable|string|max:1000',
        ], [
            'product_id.required'       => 'Debes seleccionar un producto.',
            'product_id.exists'         => 'El producto seleccionado no existe.',
            'client_name.required'      => 'El nombre es obligatorio.',
            'client_email.required'     => 'El correo electrónico es obligatorio.',
            'client_email.email'        => 'El correo electrónico no es válido.',
            'reservation_date.required' => 'La fecha es obligatoria.',
            'reservation_date.date_format' => 'Formato de fecha inválido.',
            'reservation_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'reservation_time.required' => 'La hora es obligatoria.',
            'reservation_time.date_format' => 'Formato de hora inválido (HH:MM).',
            'notes.max'                 => 'Las notas no pueden exceder 1000 caracteres.',
        ]);

        // ------------------------------------------------------------------
        // Validación de hora futura (si la fecha es hoy, la hora no puede haber pasado)
        // ------------------------------------------------------------------
        if ($request->reservation_date === Carbon::today()->format('Y-m-d')) {
            $request->validate([
                'reservation_time' => 'after:' . Carbon::now()->format('H:i'),
            ], [
                'reservation_time.after' => 'La hora debe ser posterior a la actual.',
            ]);
        }

        // ------------------------------------------------------------------
        // Validación del producto (debe estar activo)
        // ------------------------------------------------------------------
        $product = Product::where('is_active', true)->find($request->product_id);

        if (! $product) {
            return back()->withInput()
                ->withErrors(['product_id' => 'El producto seleccionado no está disponible.']);
        }

        // ------------------------------------------------------------------
        // Validación de disponibilidad en servidor (doble chequeo)
        // ------------------------------------------------------------------
        $date      = $request->reservation_date;
        $time      = $request->reservation_time;
        $dayOfWeek = strtolower(date('l', strtotime($date)));
        $businessProfileId = $product->business_profile_id;

        // Paso 1: verificar que exista un slot teórico activo que cubra esta hora
        $hasCoverage = BusinessProfile::findOrFail($businessProfileId)
            ->availabilitySlots()
            ->where('weekday', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();

        if (! $hasCoverage) {
            return back()->withInput()
                ->withErrors(['reservation_time' => 'El horario seleccionado no está dentro de la disponibilidad del vendedor.']);
        }

        // Paso 2: verificar que no haya otra reserva no cancelada en el mismo horario
        $alreadyBooked = Reservation::where('reservation_date', $date)
            ->where('reservation_time', $time)
            ->whereNotIn('status', ['cancelled'])
            ->whereHas('product', function ($query) use ($businessProfileId) {
                $query->where('business_profile_id', $businessProfileId);
            })
            ->exists();

        if ($alreadyBooked) {
            return back()->withInput()
                ->withErrors(['reservation_time' => 'Este horario ya está ocupado. Por favor elegí otro.']);
        }

        // ------------------------------------------------------------------
        // Creación de la reserva
        // ------------------------------------------------------------------
        $reservation = Reservation::create([
            'product_id'       => $product->id,
            'user_id'          => Auth::check() ? Auth::id() : null,
            'client_name'      => $request->client_name,
            'client_email'     => $request->client_email,
            'client_phone'     => $request->client_phone,
            'reservation_date' => $date,
            'reservation_time' => $time,
            'notes'            => $request->notes,
            'status'           => 'pending',
        ]);

        return redirect()->route('reservations.create')
            ->with('success', 'Reserva solicitada con éxito. Te confirmaremos pronto el turno para el ' . $date . ' a las ' . $time . '.');
    }

    /**
     * Muestra el historial de reservas del cliente autenticado.
     *
     * Las reservas se ordenan de la más reciente a la más antigua
     * para que el cliente vea primero sus próximos turnos.
     */
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

    // =========================================================================
    //  GESTIÓN DE PEDIDOS (Vendedor)
    // =========================================================================

    /**
     * Muestra la vista de gestión de pedidos del vendedor.
     */
    public function manage()
    {
        return view('reservations.manage');
    }

    /**
     * Obtiene las reservas del vendedor autenticado en formato JSON.
     *
     * Filtra por productos que pertenezcan al perfil de negocio del vendedor.
     * Aplica filtros temporales: today, tomorrow, week, month.
     */
    public function getReservations(Request $request)
    {
        $businessProfile = $request->user()->businessProfile;

        if (! $businessProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Primero debes completar tu perfil de negocio.',
            ], 403);
        }

        $query = Reservation::whereHas('product', function ($q) use ($businessProfile) {
            $q->where('business_profile_id', $businessProfile->id);
        })->with(['product', 'user']);

        // Filtro temporal
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

    // =========================================================================
    //  GESTIÓN DE ESTADOS (Panel del Vendedor)
    // =========================================================================

    /**
     * Actualiza el estado de una reserva.
     *
     * Endpoint usado por el vendedor desde su dashboard/pedidos diarios
     * para confirmar, completar o cancelar turnos en un clic.
     *
     * Solo el dueño del negocio asociado al producto puede modificar estados.
     * Responde en JSON si la petición es AJAX, o con redirect si es tradicional.
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        // Verificar que el autenticado sea dueño del negocio del producto.
        $businessProfile = $request->user()->businessProfile;
        $product = $reservation->product()->first();

        if (! $businessProfile || ! $product || $product->business_profile_id !== $businessProfile->id) {
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
}
