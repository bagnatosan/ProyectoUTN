<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test selection page is accessible for guests.
     */
    public function test_selection_page_is_accessible_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('¿Cómo deseas unirte a nosotros?');
        $response->assertSee('Soy Cliente');
        $response->assertSee('Soy Emprendedor');
    }

    /**
     * Test client registration page is accessible for guests.
     */
    public function test_client_registration_page_is_accessible_for_guests(): void
    {
        $response = $this->get('/register/client');

        $response->assertStatus(200);
        $response->assertSee('Cuenta Cliente');
        $response->assertSee('Crear cuenta nueva');
    }

    /**
     * Test seller registration page is accessible for guests.
     */
    public function test_seller_registration_page_is_accessible_for_guests(): void
    {
        $response = $this->get('/register/seller');

        $response->assertStatus(200);
        $response->assertSee('Cuenta Emprendedor');
        $response->assertSee('Registra tu Emprendimiento');
    }

    /**
     * Test client registration logs the user in and redirects to dashboard.
     */
    public function test_client_registration_saves_user_logs_in_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/register/client', [
            'name' => 'Cliente Test',
            'email' => 'client@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Cliente Test',
            'email' => 'client@test.com',
            'role' => 'client',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test seller registration logs the user in, creates profile, and redirects to dashboard.
     */
    public function test_seller_registration_saves_user_and_profile_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/register/seller', [
            'name' => 'Seller Test',
            'email' => 'seller@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Mi Negocio Test',
            'description' => 'Descripción de mi negocio',
            'phone' => '+5491122334455',
            'logo' => 'https://example.com/logo.png',
            'address' => 'Av. Siempre Viva 742',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Seller Test',
            'email' => 'seller@test.com',
            'role' => 'seller',
        ]);

        $user = User::where('email', 'seller@test.com')->first();
        $this->assertNotNull($user);

        $this->assertDatabaseHas('business_profiles', [
            'user_id' => $user->id,
            'business_name' => 'Mi Negocio Test',
            'description' => 'Descripción de mi negocio',
            'phone' => '+5491122334455',
            'logo' => 'https://example.com/logo.png',
            'address' => 'Av. Siempre Viva 742',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test guests are redirected from dashboard to role selection.
     */
    public function test_guest_redirected_from_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('register.select'));
    }

    /**
     * Test authenticated user is redirected from registration pages to dashboard.
     */
    public function test_authenticated_user_redirected_from_registration_pages(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $this->actingAs($user);

        // Selection page
        $response1 = $this->get('/');
        $response1->assertRedirect(route('dashboard'));

        // Client registration
        $response2 = $this->get('/register/client');
        $response2->assertRedirect(route('dashboard'));

        // Seller registration
        $response3 = $this->get('/register/seller');
        $response3->assertRedirect(route('dashboard'));
    }

    /**
     * Test dashboard displays client name in navbar when logged in as client.
     */
    public function test_dashboard_displays_client_name_in_navbar(): void
    {
        $user = User::factory()->create([
            'name' => 'Juan Perez',
            'role' => 'client'
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Juan Perez');
        $response->assertSee('Sesión Iniciada: Cliente');
    }

    /**
     * Test dashboard displays seller business name in navbar when logged in as seller.
     */
    public function test_dashboard_displays_seller_business_name_in_navbar(): void
    {
        $user = User::factory()->create([
            'name' => 'Maria Lopez',
            'role' => 'seller'
        ]);

        BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Pasteleria Maria',
            'description' => 'Una pastelería artesanal',
            'phone' => '+5491122334455',
            'logo' => 'https://example.com/logo.png',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Pasteleria Maria');
        $response->assertSee('Sesión Iniciada: Emprendedor');
    }
}
