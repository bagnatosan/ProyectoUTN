<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ProductCostService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::updateOrCreate(
            ['email' => 'demo.seller@proyectoutn.test'],
            [
                'name' => 'Emprendedor Demo',
                'password' => Hash::make('password'),
                'role' => 'seller',
            ]
        );

        $client = User::updateOrCreate(
            ['email' => 'demo.client@proyectoutn.test'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('password'),
                'role' => 'client',
            ]
        );

        $businessProfile = BusinessProfile::updateOrCreate(
            ['user_id' => $seller->id],
            [
                'business_name' => 'Panadería Demo UTN',
                'description' => 'Negocio de prueba para métricas, costos y reservas.',
                'phone' => '+5491122334455',
                'logo' => 'https://example.com/logo.png',
                'address' => 'Av. Demo 123',
            ]
        );

        $categories = [
            'Panificados' => Category::firstOrCreate(['name' => 'Panificados']),
            'Pastelería' => Category::firstOrCreate(['name' => 'Pastelería']),
            'Bebidas' => Category::firstOrCreate(['name' => 'Bebidas']),
        ];

        $ingredients = [
            'Harina 000' => $this->ingredient($businessProfile, 'Harina 000', 'kg', 850),
            'Azúcar' => $this->ingredient($businessProfile, 'Azúcar', 'kg', 760),
            'Manteca' => $this->ingredient($businessProfile, 'Manteca', 'kg', 3200),
            'Chocolate' => $this->ingredient($businessProfile, 'Chocolate', 'kg', 5400),
            'Café molido' => $this->ingredient($businessProfile, 'Café molido', 'kg', 9200),
            'Leche' => $this->ingredient($businessProfile, 'Leche', 'litro', 1100),
            'Té en hebras' => $this->ingredient($businessProfile, 'Té en hebras', 'kg', 7800),
        ];

        $products = [
            'Pan de campo' => Product::updateOrCreate(
                ['business_profile_id' => $businessProfile->id, 'name' => 'Pan de campo'],
                [
                    'category_id' => $categories['Panificados']->id,
                    'description' => 'Pan artesanal de masa madre.',
                    'price' => 2800,
                    'is_active' => true,
                ]
            ),
            'Brownie' => Product::updateOrCreate(
                ['business_profile_id' => $businessProfile->id, 'name' => 'Brownie'],
                [
                    'category_id' => $categories['Pastelería']->id,
                    'description' => 'Porción de brownie con chocolate.',
                    'price' => 3500,
                    'is_active' => true,
                ]
            ),
            'Café con leche' => Product::updateOrCreate(
                ['business_profile_id' => $businessProfile->id, 'name' => 'Café con leche'],
                [
                    'category_id' => $categories['Bebidas']->id,
                    'description' => 'Café espresso con leche.',
                    'price' => 1800,
                    'is_active' => true,
                ]
            ),
            'Alfajor sin receta' => Product::updateOrCreate(
                ['business_profile_id' => $businessProfile->id, 'name' => 'Alfajor sin receta'],
                [
                    'category_id' => $categories['Pastelería']->id,
                    'description' => 'Producto demo para alertar receta pendiente.',
                    'price' => 2200,
                    'estimated_cost' => null,
                    'suggested_price' => null,
                    'is_active' => true,
                ]
            ),
            'Combo margen bajo' => Product::updateOrCreate(
                ['business_profile_id' => $businessProfile->id, 'name' => 'Combo margen bajo'],
                [
                    'category_id' => $categories['Pastelería']->id,
                    'description' => 'Producto demo para detectar margen menor al 30%.',
                    'price' => 2500,
                    'is_active' => true,
                ]
            ),
            'Té artesanal sin reservas' => Product::updateOrCreate(
                ['business_profile_id' => $businessProfile->id, 'name' => 'Té artesanal sin reservas'],
                [
                    'category_id' => $categories['Bebidas']->id,
                    'description' => 'Producto demo con receta pero sin reservas.',
                    'price' => 1600,
                    'is_active' => true,
                ]
            ),
        ];

        $products['Alfajor sin receta']->ingredients()->detach();

        $this->recipe($products['Pan de campo'], [
            $ingredients['Harina 000']->id => 0.45,
        ]);
        $this->recipe($products['Brownie'], [
            $ingredients['Harina 000']->id => 0.08,
            $ingredients['Azúcar']->id => 0.12,
            $ingredients['Manteca']->id => 0.10,
            $ingredients['Chocolate']->id => 0.16,
        ]);
        $this->recipe($products['Café con leche'], [
            $ingredients['Café molido']->id => 0.02,
            $ingredients['Leche']->id => 0.20,
        ]);
        $this->recipe($products['Combo margen bajo'], [
            $ingredients['Chocolate']->id => 0.35,
            $ingredients['Manteca']->id => 0.10,
        ]);
        $this->recipe($products['Té artesanal sin reservas'], [
            $ingredients['Té en hebras']->id => 0.03,
        ]);

        Reservation::whereIn('client_email', [
            'ana.demo@example.com',
            'bruno.demo@example.com',
            'carla.demo@example.com',
            'diego.demo@example.com',
            'elena.demo@example.com',
            'federico.demo@example.com',
            'hugo.demo@example.com',
        ])->delete();

        $this->reservation($products['Pan de campo'], $client, 'Ana Gómez', 'ana.demo@example.com', now()->toDateString(), '09:00', 'pending');
        $this->reservation($products['Brownie'], $client, 'Bruno Pérez', 'bruno.demo@example.com', now()->toDateString(), '11:30', 'confirmed');
        $this->reservation($products['Café con leche'], $client, 'Carla Ruiz', 'carla.demo@example.com', now()->subDays(2)->toDateString(), '10:15', 'completed');
        $this->reservation($products['Brownie'], $client, 'Diego López', 'diego.demo@example.com', now()->subDays(5)->toDateString(), '16:00', 'completed');
        $this->reservation($products['Pan de campo'], $client, 'Elena Torres', 'elena.demo@example.com', now()->addDay()->toDateString(), '14:00', 'cancelled');
        $this->reservation($products['Brownie'], $client, 'Federico Silva', 'federico.demo@example.com', now()->addDays(3)->toDateString(), '17:30', 'pending');
        $this->reservation($products['Combo margen bajo'], $client, 'Hugo Méndez', 'hugo.demo@example.com', now()->subMonths(2)->toDateString(), '10:45', 'completed');
    }

    private function ingredient(BusinessProfile $businessProfile, string $name, string $unitMeasure, float $unitCost): Ingredient
    {
        return Ingredient::updateOrCreate(
            ['business_profile_id' => $businessProfile->id, 'name' => $name],
            [
                'unit_measure' => $unitMeasure,
                'unit_cost' => $unitCost,
            ]
        );
    }

    private function recipe(Product $product, array $ingredientQuantities): void
    {
        foreach ($ingredientQuantities as $ingredientId => $quantity) {
            ProductIngredient::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'ingredient_id' => $ingredientId,
                ],
                ['quantity' => $quantity]
            );
        }

        app(ProductCostService::class)->update($product);
    }

    private function reservation(
        Product $product,
        User $client,
        string $clientName,
        string $clientEmail,
        string $reservationDate,
        string $reservationTime,
        string $status
    ): void {
        $reservationTime = strlen($reservationTime) === 5 ? "{$reservationTime}:00" : $reservationTime;

        Reservation::updateOrCreate(
            [
                'product_id' => $product->id,
                'client_email' => $clientEmail,
                'reservation_date' => $reservationDate,
                'reservation_time' => $reservationTime,
            ],
            [
                'user_id' => $client->id,
                'client_name' => $clientName,
                'client_phone' => '+5491100000000',
                'notes' => 'Datos demo Programador 5',
                'status' => $status,
            ]
        );
    }
}
