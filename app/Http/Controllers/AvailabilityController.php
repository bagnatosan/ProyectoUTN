<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreAvailabilitySlotsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService,
    ) {}

    public function index(Request $request): View
    {
        $slots = $request->user()->availabilitySlots()
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view('availability.edit', compact('slots'));
    }

    public function store(StoreAvailabilitySlotsRequest $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user, $request) {
            $user->availabilitySlots()->delete();

            foreach ($request->slots as $slot) {
                $user->availabilitySlots()->create([
                    'day_of_week' => $slot['day_of_week'],
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Disponibilidad guardada exitosamente.']);
        }

        return redirect()->route('availability.index')
            ->with('success', 'Disponibilidad horaria guardada exitosamente.');
    }

    public function update(StoreAvailabilitySlotsRequest $request)
    {
        return $this->store($request);
    }

    public function availableSlots(int $sellerId, string $date, Request $request): JsonResponse
    {
        $seller = User::find($sellerId);

        if (!$seller || $seller->role !== 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'El vendedor no existe.',
            ], 404);
        }

        $excludeReservation = $request->integer('exclude_reservation', 0) ?: null;

        $slots = $this->availabilityService->getAvailableSlots($sellerId, $date, $excludeReservation);

        return response()->json([
            'success' => true,
            'date'    => $date,
            'slots'   => $slots,
        ]);
    }
}
