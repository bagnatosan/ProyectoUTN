<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // VENDEDOR 1: Pastelería de Prueba
        // -------------------------------------------------------------
        $userId1 = DB::table('users')->insertGetId([
            'name' => 'Vendedor de Prueba',
            'email' => 'vendedor@prueba.com',
            'role' => 'seller', // Corrección del bug original: se agrega el rol correcto
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $businessId1 = DB::table('business_profiles')->insertGetId([
            'user_id' => $userId1,
            'business_name' => 'Pastelería de Prueba',
            'description' => 'Un negocio de prueba para probar el constructor de recetas',
            'address' => 'Calle Falsa 123',
            'phone' => '1122223333',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ingredients1 = [
            ['business_profile_id' => $businessId1, 'name' => 'Harina 0000', 'unit_measure' => 'kg', 'unit_cost' => 1200.00],
            ['business_profile_id' => $businessId1, 'name' => 'Azúcar Blanco', 'unit_measure' => 'kg', 'unit_cost' => 1000.00],
            ['business_profile_id' => $businessId1, 'name' => 'Huevos', 'unit_measure' => 'docena', 'unit_cost' => 2400.00],
            ['business_profile_id' => $businessId1, 'name' => 'Manteca', 'unit_measure' => 'g', 'unit_cost' => 1500.00],
            ['business_profile_id' => $businessId1, 'name' => 'Chocolate Semiamargo', 'unit_measure' => 'kg', 'unit_cost' => 8500.00],
            ['business_profile_id' => $businessId1, 'name' => 'Esencia de Vainilla', 'unit_measure' => 'ml', 'unit_cost' => 500.00],
            ['business_profile_id' => $businessId1, 'name' => 'Leche Entera', 'unit_measure' => 'litro', 'unit_cost' => 1100.00],
            ['business_profile_id' => $businessId1, 'name' => 'Dulce de Leche Repostero', 'unit_measure' => 'kg', 'unit_cost' => 3800.00],
        ];

        foreach ($ingredients1 as $ingredient) {
            Ingredient::create($ingredient);
        }

        // -------------------------------------------------------------
        // VENDEDOR 2: Panadería Don Julio
        // -------------------------------------------------------------
        $userId2 = DB::table('users')->insertGetId([
            'name' => 'Don Julio',
            'email' => 'julio@panaderia.com',
            'role' => 'seller',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $businessId2 = DB::table('business_profiles')->insertGetId([
            'user_id' => $userId2,
            'business_name' => 'Panadería Don Julio',
            'description' => 'Panes artesanales de masa madre y facturas tradicionales hechas a mano.',
            'address' => 'Av. Siempreviva 742',
            'phone' => '1133334444',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ingredients2 = [
            ['business_profile_id' => $businessId2, 'name' => 'Harina de Trigo', 'unit_measure' => 'kg', 'unit_cost' => 900.00],
            ['business_profile_id' => $businessId2, 'name' => 'Levadura Seca', 'unit_measure' => 'g', 'unit_cost' => 3.00],
            ['business_profile_id' => $businessId2, 'name' => 'Sal Fina', 'unit_measure' => 'kg', 'unit_cost' => 800.00],
            ['business_profile_id' => $businessId2, 'name' => 'Agua', 'unit_measure' => 'litro', 'unit_cost' => 10.00],
            ['business_profile_id' => $businessId2, 'name' => 'Grasa Vacuna', 'unit_measure' => 'g', 'unit_cost' => 4.00],
        ];

        foreach ($ingredients2 as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}