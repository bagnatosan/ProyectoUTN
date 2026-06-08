<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Product;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $reservations = Auth::user()
            ->reservations()
            ->with('product')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();

        return view('reservations.client_history', compact('reservations'));
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
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        // Verificar que el autenticado sea dueño del negocio del producto
        $businessProfile = $request->user()->businessProfile;

        if (! $businessProfile || $reservation->product->business_profile_id !== $businessProfile->id) {
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

        return back()->with('success', 'Reserva #' . $reservation->id . ' actualizada a "' . ($statusLabels[$request->status] ?? $request->status) . '".');
    }
}
