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
        // 1. Creamos un Usuario de prueba (obligatorio para el negocio)
        $userId = DB::table('users')->insertGetId([
            'name' => 'Vendedor de Prueba',
            'email' => 'vendedor@prueba.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Creamos el Perfil de Negocio usando 'business_name' y vinculando el 'user_id'
        $businessId = DB::table('business_profiles')->insertGetId([
            'user_id' => $userId,
            'business_name' => 'Pastelería de Prueba',
            'description' => 'Un negocio de prueba para probar el constructor de recetas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insertamos tus ingredientes con las columnas exactas de tus compañeros
        $ingredients = [
            ['business_profile_id' => $businessId, 'name' => 'Harina 0000', 'unit_measure' => 'kg', 'unit_cost' => 1200.00],
            ['business_profile_id' => $businessId, 'name' => 'Azúcar Blanco', 'unit_measure' => 'kg', 'unit_cost' => 1000.00],
            ['business_profile_id' => $businessId, 'name' => 'Huevos', 'unit_measure' => 'docena', 'unit_cost' => 2400.00],
            ['business_profile_id' => $businessId, 'name' => 'Manteca', 'unit_measure' => 'g', 'unit_cost' => 1500.00],
            ['business_profile_id' => $businessId, 'name' => 'Chocolate Semiamargo', 'unit_measure' => 'kg', 'unit_cost' => 8500.00],
            ['business_profile_id' => $businessId, 'name' => 'Esencia de Vainilla', 'unit_measure' => 'ml', 'unit_cost' => 500.00],
            ['business_profile_id' => $businessId, 'name' => 'Leche Entera', 'unit_measure' => 'litro', 'unit_cost' => 1100.00],
            ['business_profile_id' => $businessId, 'name' => 'Dulce de Leche Repostero', 'unit_measure' => 'kg', 'unit_cost' => 3800.00],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}