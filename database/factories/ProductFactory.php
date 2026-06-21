<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'business_profile_id' => BusinessProfile::factory(),
            'category_id'         => Category::factory(),
            'name'                => fake()->words(3, true),
            'description'         => fake()->optional(0.7)->sentence(),
            'price'               => fake()->randomFloat(2, 500, 5000),
            'image'               => null,
            'estimated_cost'      => 0,
            'suggested_price'     => 0,
            'is_active'           => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
