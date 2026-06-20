<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // User::factory(10)->create();

    // Dejamos la creación del usuario de prueba por defecto
    User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

   
    $this->call([
        IngredientSeeder::class,
        //CategorySeeder::class,
        //ProductSeeder::class,
        //AvailabilitySlotSeeder::class,   
        //ReservationSeeder::class,        
    ]);
}
}
