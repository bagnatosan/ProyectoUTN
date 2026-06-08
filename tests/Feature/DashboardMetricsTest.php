<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_dashboard_calculates_metrics_for_own_business_only(): void
    {
        [$seller, $businessProfile] = $this->createSellerBusiness('Panaderia Norte');
        $product = $this->createProduct($businessProfile, 'Pan integral', 100, 30);
        $otherProduct = $this->createProduct($this->createSellerBusiness('Otro negocio')[1], 'Producto externo', 999, 100);

        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Ana Cliente',
            'client_email' => 'ana@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '10:00',
            'status' => 'completed',
        ]);
        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Bruno Cliente',
            'client_email' => 'bruno@example.com',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '12:00',
            'status' => 'pending',
        ]);
        Reservation::create([
            'product_id' => $otherProduct->id,
            'client_name' => 'Cliente Externo',
            'client_email' => 'externo@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '14:00',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($seller)->get(route('dashboard.metrics'));

        $response->assertStatus(200);
        $response->assertViewHas('metrics', function (array $metrics) {
            return $metrics['monthly_revenue'] === 100.0
                && $metrics['monthly_profit'] === 70.0
                && $metrics['active_reservations'] === 1
                && $metrics['status_counts']['completed'] === 1
                && $metrics['status_counts']['pending'] === 1;
        });
        $response->assertSee('Ana Cliente');
        $response->assertSee('Bruno Cliente');
        $response->assertDontSee('Cliente Externo');
    }

    public function test_seller_dashboard_filters_reservations(): void
    {
        [$seller, $businessProfile] = $this->createSellerBusiness('Pasteleria Sur');
        $product = $this->createProduct($businessProfile, 'Torta brownie', 150, 50);

        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Ana Gomez',
            'client_email' => 'ana@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '10:00',
            'status' => 'completed',
        ]);
        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Carlos Ruiz',
            'client_email' => 'carlos@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '11:00',
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($seller)->get(route('dashboard.metrics', [
            'search' => 'Ana',
            'status' => 'completed',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reservations', function ($reservations) {
            return $reservations->count() === 1
                && $reservations->first()->client_name === 'Ana Gomez';
        });
    }

    public function test_seller_dashboard_filters_reservations_by_date_range(): void
    {
        [$seller, $businessProfile] = $this->createSellerBusiness('Fechas Test');
        $product = $this->createProduct($businessProfile, 'Producto con fecha', 100, 30);

        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Dentro del Rango',
            'client_email' => 'dentro@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '10:00',
            'status' => 'pending',
        ]);
        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Fuera del Rango',
            'client_email' => 'fuera@example.com',
            'reservation_date' => now()->subDays(10)->toDateString(),
            'reservation_time' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($seller)->get(route('dashboard.metrics', [
            'date_from' => now()->subDay()->toDateString(),
            'date_to' => now()->addDay()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Dentro del Rango');
        $response->assertDontSee('Fuera del Rango');
    }

    public function test_seller_dashboard_filters_today_orders_by_status(): void
    {
        [$seller, $businessProfile] = $this->createSellerBusiness('Pedidos Hoy Test');
        $product = $this->createProduct($businessProfile, 'Producto del dia', 120, 40);

        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Pedido Pendiente Hoy',
            'client_email' => 'pendiente@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '09:00',
            'status' => 'pending',
        ]);
        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Pedido Confirmado Hoy',
            'client_email' => 'confirmado@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '11:00',
            'status' => 'confirmed',
        ]);
        Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Pedido Manana',
            'client_email' => 'manana@example.com',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '12:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($seller)->get(route('dashboard.metrics', [
            'today_status' => 'pending',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('todayReservations', function ($todayReservations) {
            return $todayReservations->count() === 1
                && $todayReservations->first()->client_name === 'Pedido Pendiente Hoy';
        });
    }

    public function test_dashboard_rejects_invalid_status_filter(): void
    {
        [$seller] = $this->createSellerBusiness('Estado Invalido Test');

        $response = $this
            ->actingAs($seller)
            ->from(route('dashboard.metrics'))
            ->get(route('dashboard.metrics', ['status' => 'invalid']));

        $response->assertRedirect(route('dashboard.metrics'));
        $response->assertSessionHasErrors('status');
    }

    public function test_dashboard_rejects_invalid_today_status_filter(): void
    {
        [$seller] = $this->createSellerBusiness('Estado Hoy Invalido Test');

        $response = $this
            ->actingAs($seller)
            ->from(route('dashboard.metrics'))
            ->get(route('dashboard.metrics', ['today_status' => 'invalid']));

        $response->assertRedirect(route('dashboard.metrics'));
        $response->assertSessionHasErrors('today_status');
    }

    public function test_dashboard_rejects_invalid_date_range(): void
    {
        [$seller] = $this->createSellerBusiness('Fechas Invalidas Test');

        $response = $this
            ->actingAs($seller)
            ->from(route('dashboard.metrics'))
            ->get(route('dashboard.metrics', [
                'date_from' => now()->toDateString(),
                'date_to' => now()->subDay()->toDateString(),
            ]));

        $response->assertRedirect(route('dashboard.metrics'));
        $response->assertSessionHasErrors('date_to');
    }

    public function test_seller_can_update_status_for_own_reservation(): void
    {
        [$seller, $businessProfile] = $this->createSellerBusiness('Cafe Centro');
        $product = $this->createProduct($businessProfile, 'Cafe con leche', 80, 20);
        $reservation = Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Cliente Reserva',
            'client_email' => 'cliente@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '09:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($seller)->patch(route('dashboard.reservations.status', $reservation), [
            'status' => 'confirmed',
        ]);

        $response->assertRedirect(route('dashboard.metrics'));
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_seller_cannot_update_reservation_to_invalid_status(): void
    {
        [$seller, $businessProfile] = $this->createSellerBusiness('Cambio Invalido Test');
        $product = $this->createProduct($businessProfile, 'Producto cambio invalido', 80, 20);
        $reservation = Reservation::create([
            'product_id' => $product->id,
            'client_name' => 'Cliente Estado Invalido',
            'client_email' => 'estado-invalido@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '09:00',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($seller)
            ->from(route('dashboard.metrics'))
            ->patch(route('dashboard.reservations.status', $reservation), [
                'status' => 'invalid',
            ]);

        $response->assertRedirect(route('dashboard.metrics'));
        $response->assertSessionHasErrors('status');
    }

    public function test_seller_cannot_update_another_business_reservation(): void
    {
        [$seller] = $this->createSellerBusiness('Negocio propio');
        $otherProduct = $this->createProduct($this->createSellerBusiness('Negocio ajeno')[1], 'Producto ajeno', 200, 80);
        $reservation = Reservation::create([
            'product_id' => $otherProduct->id,
            'client_name' => 'Cliente Ajeno',
            'client_email' => 'ajeno@example.com',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '15:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($seller)->patch(route('dashboard.reservations.status', $reservation), [
            'status' => 'confirmed',
        ]);

        $response->assertForbidden();
    }

    public function test_seller_without_business_profile_cannot_access_dashboard(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($seller)->get(route('dashboard.metrics'));

        $response->assertForbidden();
    }

    public function test_client_cannot_access_seller_dashboard(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get(route('dashboard.metrics'));

        $response->assertForbidden();
    }

    private function createSellerBusiness(string $businessName): array
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $businessProfile = BusinessProfile::create([
            'user_id' => $seller->id,
            'business_name' => $businessName,
            'description' => 'Negocio de prueba',
            'phone' => '123456789',
            'logo' => 'https://example.com/logo.png',
        ]);

        return [$seller, $businessProfile];
    }

    private function createProduct(BusinessProfile $businessProfile, string $name, float $price, float $estimatedCost): Product
    {
        $category = Category::firstOrCreate(['name' => 'General']);

        return Product::create([
            'business_profile_id' => $businessProfile->id,
            'category_id' => $category->id,
            'name' => $name,
            'price' => $price,
            'estimated_cost' => $estimatedCost,
            'suggested_price' => $estimatedCost * 3,
            'is_active' => true,
        ]);
    }
}
