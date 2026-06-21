<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'product_id'       => Product::factory(),
            'user_id'          => User::factory()->client(),
            'client_name'      => fake()->name(),
            'client_email'     => fake()->safeEmail(),
            'client_phone'     => fake()->phoneNumber(),
            'reservation_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'reservation_time' => fake()->randomElement(['09:00', '10:00', '11:00', '14:00', '15:00', '16:00']),
            'notes'            => fake()->optional(0.3)->sentence(),
            'status'           => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status'             => 'cancelled',
            'cancellation_reason' => fake()->sentence(),
            'cancelled_by'       => fake()->randomElement(['client', 'seller']),
        ]);
    }
}
