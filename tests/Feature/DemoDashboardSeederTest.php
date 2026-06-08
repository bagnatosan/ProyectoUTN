<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Reservation;
use Database\Seeders\DemoDashboardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDashboardSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_dashboard_seeder_creates_realistic_dashboard_data(): void
    {
        $this->seed(DemoDashboardSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'demo.seller@proyectoutn.test',
            'role' => 'seller',
        ]);
        $this->assertDatabaseHas('business_profiles', [
            'business_name' => 'Panadería Demo UTN',
        ]);
        $this->assertSame(6, Product::count());
        $this->assertSame(7, Reservation::count());
        $this->assertTrue(Product::whereNotNull('estimated_cost')->where('estimated_cost', '>', 0)->exists());
        $this->assertTrue(Product::whereNotNull('suggested_price')->where('suggested_price', '>', 0)->exists());
        $this->assertDatabaseHas('products', [
            'name' => 'Alfajor sin receta',
            'estimated_cost' => null,
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Combo margen bajo',
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Té artesanal sin reservas',
        ]);
        $this->assertTrue(Reservation::whereIn('status', ['pending', 'confirmed', 'completed', 'cancelled'])->exists());
    }

    public function test_demo_dashboard_seeder_is_idempotent(): void
    {
        $this->seed(DemoDashboardSeeder::class);

        $countsAfterFirstRun = [
            'products' => Product::count(),
            'reservations' => Reservation::count(),
        ];

        $this->seed(DemoDashboardSeeder::class);

        $this->assertSame($countsAfterFirstRun['products'], Product::count());
        $this->assertSame($countsAfterFirstRun['reservations'], Reservation::count());
    }
}
