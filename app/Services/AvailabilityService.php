<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\BusinessProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    private const SLOT_INTERVAL_MINUTES = 30;

    public function getAvailableSlots(int $businessProfileId, string $date): array
    {
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        $theoreticalSlots = BusinessProfile::findOrFail($businessProfileId)
            ->availabilitySlots()
            ->where('weekday', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($theoreticalSlots->isEmpty()) {
            return [
                'date'    => $date,
                'day'     => $dayOfWeek,
                'slots'   => [],
                'message' => 'No hay horarios configurados para este día.',
            ];
        }

        $allSlots = collect();
        foreach ($theoreticalSlots as $slot) {
            $start = Carbon::parse($slot->start_time);
            $end   = Carbon::parse($slot->end_time);

            while ($start->copy()->addMinutes(self::SLOT_INTERVAL_MINUTES)->lte($end)) {
                $allSlots->push($start->format('H:i'));
                $start->addMinutes(self::SLOT_INTERVAL_MINUTES);
            }
        }

        $occupiedTimes = Reservation::where('reservation_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->whereHas('product', function ($query) use ($businessProfileId) {
                $query->where('business_profile_id', $businessProfileId);
            })
            ->pluck('reservation_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'));

        $freeSlots = $allSlots->reject(fn ($slot) => $occupiedTimes->contains($slot))->values();

        return [
            'date'    => $date,
            'day'     => $dayOfWeek,
            'slots'   => $freeSlots->toArray(),
            'message' => $freeSlots->isNotEmpty()
                ? $freeSlots->count() . ' horarios disponibles.'
                : 'No hay horarios disponibles para esta fecha.',
        ];
    }

    public function isSlotAvailable(int $businessProfileId, string $date, string $time): bool
    {
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        $hasCoverage = BusinessProfile::findOrFail($businessProfileId)
            ->availabilitySlots()
            ->where('weekday', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();

        if (!$hasCoverage) {
            return false;
        }

        $alreadyBooked = Reservation::where('reservation_date', $date)
            ->where('reservation_time', $time)
            ->whereNotIn('status', ['cancelled'])
            ->whereHas('product', function ($query) use ($businessProfileId) {
                $query->where('business_profile_id', $businessProfileId);
            })
            ->lockForUpdate()
            ->exists();

        return !$alreadyBooked;
    }
}
