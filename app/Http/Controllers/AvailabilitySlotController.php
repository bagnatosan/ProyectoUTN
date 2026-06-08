<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Eliminación masiva: borra todos los slots del negocio en una sola query.
        // Es más eficiente que iterar y eliminar uno por uno.
        $businessProfile->availabilitySlots()->delete();

        // Inserción masiva de los nuevos slots recibidos.
        // Se marcan como activos por defecto al crearse.
        foreach ($request->slots as $slot) {
            $businessProfile->availabilitySlots()->create([
                'weekday'    => $slot['weekday'],
                'start_time' => $slot['start_time'],
                'end_time'   => $slot['end_time'],
                'is_active'  => true,
            ]);
        }

        return redirect()->route('availability.edit')
            ->with('success', 'Disponibilidad horaria guardada exitosamente.');
    }
}
