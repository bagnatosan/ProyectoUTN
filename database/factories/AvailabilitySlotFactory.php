<?php

namespace Database\Factories;

use App\Models\AvailabilitySlot;
use App\Models\BusinessProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilitySlotFactory extends Factory
{
    protected $model = AvailabilitySlot::class;

    public function definition(): array
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return [
            'business_profile_id' => BusinessProfile::factory(),
            'weekday'             => fake()->randomElement($weekdays),
            'start_time'          => fake()->randomElement(['09:00', '10:00', '14:00']),
            'end_time'            => fake()->randomElement(['13:00', '17:00', '20:00']),
            'is_active'           => true,
        ];
    }
}
