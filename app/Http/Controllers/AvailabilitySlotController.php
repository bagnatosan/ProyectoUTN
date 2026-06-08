<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AvailabilitySlotController extends Controller
{
    /**
     * Días de la semana válidos para la agenda del vendedor.
     *
     * Centralizados como constante para reutilización en validación
     * y evitar strings mágicos dispersos en el controlador.
     * El orden sigue el estándar ISO (lunes=primero).
     */
    private const WEEKDAYS = [
        'monday', 'tuesday', 'wednesday', 'thursday',
        'friday', 'saturday', 'sunday'
    ];

    /**
     * Intervalo en minutos entre cada slot disponible.
     *
     * Define la granularidad de la agenda. 30 minutos es el estándar
     * para sistemas de turnos. Si se necesita cambiar (ej: 15 o 60 min),
     * solo se modifica esta constante.
     */
    private const SLOT_INTERVAL_MINUTES = 30;

    /**
     * Muestra el formulario de configuración de la agenda semanal.
     *
     * Carga los slots existentes del negocio autenticado, ordenados
     * por hora y agrupados por día para facilitar el renderizado
     * en la vista (cada día con su lista de franjas).
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request)
    {
        $businessProfile = $request->user()->businessProfile;

        // El perfil de negocio es requisito previo: sin él no hay agenda que configurar.
        if (! $businessProfile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Primero debes completar tu perfil de negocio.');
        }

        // Se agrupa por weekday para que la vista pueda iterar día por día.
        $slots = $businessProfile->availabilitySlots()->orderBy('start_time')->get()->groupBy('weekday');

        return view('availability.edit', compact('businessProfile', 'slots'));
    }

    /**
     * Reemplaza por completo la agenda semanal del vendedor.
     *
     * Estrategia "delete-and-reinsert": se eliminan todos los slots
     * existentes y se crean los nuevos recibidos. Esto evita tener
     * que hacer diff/update individual por cada franja, simplifica
     * la lógica y previene bugs de sincronización cuando el vendedor
     * modifica varios días a la vez.
     *
     * NOTA: Si a futuro se necesita historial de cambios, esta estrategia
     * debería migrarse a soft-deletes o versionado de slots.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $businessProfile = $request->user()->businessProfile;

        if (! $businessProfile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Primero debes completar tu perfil de negocio.');
        }

        // Validación completa del array de slots.
        // Se validan: día válido, formato de hora HH:MM, y que end > start.
        // La validación 'after:slots.*.start_time' compara por pares dentro del mismo slot.
        $request->validate([
            'slots' => 'required|array|min:1',
            'slots.*.weekday' => ['required', 'string', 'in:' . implode(',', self::WEEKDAYS)],
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time',
        ], [
            'slots.required' => 'Debes agregar al menos un horario de atención.',
            'slots.*.weekday.in' => 'El día de la semana no es válido.',
            'slots.*.start_time.date_format' => 'La hora de inicio debe tener formato HH:MM.',
            'slots.*.end_time.date_format' => 'La hora de fin debe tener formato HH:MM.',
            'slots.*.end_time.after' => 'La hora de fin debe ser posterior a la de inicio.',
        ]);

        // Transacción: si falla la inserción, se revierte el delete.
        // Esto evita perder la agenda si hay un error a mitad del reemplazo.
        DB::transaction(function () use ($businessProfile, $request) {
            $businessProfile->availabilitySlots()->delete();

            foreach ($request->slots as $slot) {
                $businessProfile->availabilitySlots()->create([
                    'weekday'    => $slot['weekday'],
                    'start_time' => $slot['start_time'],
                    'end_time'   => $slot['end_time'],
                    'is_active'  => true,
                ]);
            }
        });

        return redirect()->route('availability.edit')
            ->with('success', 'Disponibilidad horaria guardada exitosamente.');
    }

    // =========================================================================
    //  MOTOR DE DISPONIBILIDAD (ALGORITMO PRINCIPAL)
    // =========================================================================

    /**
     * Obtiene los horarios libres para una fecha y negocio dados.
     *
     * ALGORITMO:
     * 1. Determina el día de la semana de la fecha solicitada.
     * 2. Busca los horarios teóricos (availability_slots) configurados para ese día.
     * 3. Genera bloques de tiempo discretos (cada SLOT_INTERVAL_MINUTES min).
     * 4. Busca reservas existentes para esa fecha (excluye canceladas).
     * 5. Filtra los bloques ocupados y devuelve solo los libres.
     *
     * Endpoint público consumido vía Fetch desde el calendario del formulario
     * de reservas. Responde en JSON para que el frontend lo renderice.
     *
     * @param Request $request  Espera: business_profile_id, date (Y-m-d)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots(Request $request)
    {
        // ------------------------------------------------------------------
        // Validación de entrada
        // ------------------------------------------------------------------
        $request->validate([
            'business_profile_id' => 'required|integer|exists:business_profiles,id',
            'date'                => 'required|date_format:Y-m-d|after_or_equal:today',
        ], [
            'business_profile_id.required' => 'El negocio es requerido.',
            'business_profile_id.exists'   => 'El negocio no existe.',
            'date.required'                => 'La fecha es requerida.',
            'date.date_format'             => 'Formato de fecha inválido (Y-m-d).',
            'date.after_or_equal'          => 'La fecha no puede ser anterior a hoy.',
        ]);

        $businessProfileId = $request->input('business_profile_id');
        $date              = $request->input('date');

        // ------------------------------------------------------------------
        // Paso 1: determinar el día de la semana en inglés (monday, tuesday…)
        // ------------------------------------------------------------------
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        // ------------------------------------------------------------------
        // Paso 2: obtener los horarios teóricos configurados para ese día
        // ------------------------------------------------------------------
        $theoreticalSlots = BusinessProfile::findOrFail($businessProfileId)
            ->availabilitySlots()
            ->where('weekday', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($theoreticalSlots->isEmpty()) {
            return response()->json([
                'date'     => $date,
                'day'      => $dayOfWeek,
                'slots'    => [],
                'message'  => 'No hay horarios configurados para este día.',
            ]);
        }

        // ------------------------------------------------------------------
        // Paso 3: generar todos los bloques de tiempo posibles
        // ------------------------------------------------------------------
        $allSlots = collect();
        foreach ($theoreticalSlots as $slot) {
            $start = Carbon::parse($slot->start_time);
            $end   = Carbon::parse($slot->end_time);

            while ($start->copy()->addMinutes(self::SLOT_INTERVAL_MINUTES)->lte($end)) {
                $allSlots->push($start->format('H:i'));
                $start->addMinutes(self::SLOT_INTERVAL_MINUTES);
            }
        }

        // ------------------------------------------------------------------
        // Paso 4: obtener horarios ocupados (reservas no canceladas)
        // ------------------------------------------------------------------
        $occupiedTimes = Reservation::where('reservation_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->whereHas('product', function ($query) use ($businessProfileId) {
                $query->where('business_profile_id', $businessProfileId);
            })
            ->pluck('reservation_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'));

        // ------------------------------------------------------------------
        // Paso 5: filtrar bloques libres (los no ocupados)
        // ------------------------------------------------------------------
        $freeSlots = $allSlots->reject(fn ($slot) => $occupiedTimes->contains($slot))->values();

        return response()->json([
            'date'    => $date,
            'day'     => $dayOfWeek,
            'slots'   => $freeSlots,
            'message' => $freeSlots->isNotEmpty()
                ? $freeSlots->count() . ' horarios disponibles.'
                : 'No hay horarios disponibles para esta fecha.',
        ]);
    }
}
