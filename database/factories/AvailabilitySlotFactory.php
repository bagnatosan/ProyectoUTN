<?php

namespace Database\Factories;

use App\Models\AvailabilitySlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilitySlotFactory extends Factory
{
    protected $model = AvailabilitySlot::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'start_time'  => fake()->randomElement(['09:00', '10:00', '14:00']),
            'end_time'    => fake()->randomElement(['13:00', '17:00', '20:00']),
        ];
    }
}
