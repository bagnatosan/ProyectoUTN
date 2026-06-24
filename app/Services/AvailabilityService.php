<?php

namespace App\Services;

use App\Models\AvailabilitySlot;
use App\Models\Reservation;
use Carbon\Carbon;

class AvailabilityService
{
    private const SLOT_INTERVAL_MINUTES = 30;

    /**
     * Obtiene los horarios libres para un vendedor en una fecha dada.
     *
     * Algoritmo:
     * 1. Determina el día de la semana de la fecha.
     * 2. Obtiene las franjas de disponibilidad del vendedor para ese día.
     * 3. Genera slots discretos de 30 minutos dentro de cada franja.
     * 4. Obtiene las reservas activas (pending/confirmed) del vendedor para esa fecha.
     * 5. Filtra slots ocupados.
     * 6. Si es hoy, solo devuelve horarios futuros.
     *
     * @param int    $sellerId ID del vendedor (User::id con rol seller).
     * @param string $date     Fecha en formato Y-m-d.
     * @return array Lista de horarios disponibles en formato H:i (ej: ["09:00", "09:30"]).
     */
    public function getAvailableSlots(int $sellerId, string $date, ?int $excludeReservationId = null): array
    {
        $dateCarbon = Carbon::parse($date);

        // No se permiten fechas pasadas
        if ($dateCarbon->isPast() && !$dateCarbon->isToday()) {
            return [];
        }

        $dayOfWeek = (int) $dateCarbon->format('w');

        // 1. Obtener franjas teóricas de disponibilidad para ese día
        $theoreticalSlots = AvailabilitySlot::forUser($sellerId)
            ->forDay($dayOfWeek)
            ->orderBy('start_time')
            ->get();

        if ($theoreticalSlots->isEmpty()) {
            return [];
        }

        // 2. Generar todos los slots de 30 minutos dentro de las franjas
        $allSlots = collect();
        foreach ($theoreticalSlots as $slot) {
            $start = Carbon::parse($slot->start_time);
            $end   = Carbon::parse($slot->end_time);

            while ($start->copy()->addMinutes(self::SLOT_INTERVAL_MINUTES)->lte($end)) {
                $allSlots->push($start->format('H:i'));
                $start->addMinutes(self::SLOT_INTERVAL_MINUTES);
            }
        }

        // 3. Obtener horarios ocupados por reservas activas del vendedor
        $occupiedTimes = Reservation::where('reservation_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('product', function ($q) use ($sellerId) {
                $q->whereHas('businessProfile', fn ($q2) => $q2->where('user_id', $sellerId));
            })
            ->when($excludeReservationId, fn ($q) => $q->where('id', '!=', $excludeReservationId))
            ->pluck('reservation_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'));

        // 4. Filtrar slots ocupados
        $freeSlots = $allSlots->reject(
            fn ($slot) => $occupiedTimes->contains($slot)
        );

        // 5. Si es hoy, filtrar solo horarios posteriores a ahora
        if ($dateCarbon->isToday()) {
            $now = Carbon::now();
            $freeSlots = $freeSlots->filter(
                fn ($slot) => $slot > $now->format('H:i')
            );
        }

        return $freeSlots->values()->toArray();
    }

    /**
     * Verifica si un horario específico está disponible para un vendedor en una fecha dada.
     *
     * @param int    $sellerId ID del vendedor.
     * @param string $date     Fecha en formato Y-m-d.
     * @param string $time     Hora en formato H:i.
     * @return bool True si el horario está libre.
     */
    public function isSlotAvailable(int $sellerId, string $date, string $time, ?int $excludeReservationId = null): bool
    {
        $dayOfWeek = (int) Carbon::parse($date)->format('w');

        // Verificar que exista cobertura en la franja horaria
        $hasCoverage = AvailabilitySlot::forUser($sellerId)
            ->forDay($dayOfWeek)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();

        if (!$hasCoverage) {
            return false;
        }

        // Verificar que no esté ocupado por una reserva activa
        $alreadyBooked = Reservation::where('reservation_date', $date)
            ->where('reservation_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('product', function ($q) use ($sellerId) {
                $q->whereHas('businessProfile', fn ($q2) => $q2->where('user_id', $sellerId));
            })
            ->when($excludeReservationId, fn ($q) => $q->where('id', '!=', $excludeReservationId))
            ->lockForUpdate()
            ->exists();

        return !$alreadyBooked;
    }
}
