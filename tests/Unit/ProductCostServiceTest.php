<?php

namespace Tests\Unit;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use App\Models\User;
use App\Services\ProductCostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_product_estimated_cost_from_recipe_quantities(): void
    {
        $product = $this->createProduct();
        $flour = $this->createIngredient($product->businessProfile, 'Harina', 120);
        $butter = $this->createIngredient($product->businessProfile, 'Manteca', 800);

        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $flour->id,
            'quantity' => 0.5,
        ]);
        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $butter->id,
            'quantity' => 0.25,
        ]);

        $costs = app(ProductCostService::class)->calculate($product);

        $this->assertSame(260.0, $costs['estimated_cost']);
    }

    public function test_it_calculates_suggested_price_as_three_times_estimated_cost(): void
    {
        $product = $this->createProduct();
        $ingredient = $this->createIngredient($product->businessProfile, 'Chocolate', 500);

        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 0.4,
        ]);

        $costs = app(ProductCostService::class)->calculate($product);

        $this->assertSame(200.0, $costs['estimated_cost']);
        $this->assertSame(600.0, $costs['suggested_price']);
    }

    private function createProduct(): Product
    {
        $user = User::factory()->create(['role' => 'seller']);
        $businessProfile = BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Costos Unit Test',
            'description' => 'Negocio para pruebas unitarias',
            'phone' => '123456789',
            'logo' => 'https://example.com/logo.png',
        ]);
        $category = Category::create(['name' => 'Unit Test']);

        return Product::create([
            'business_profile_id' => $businessProfile->id,
            'category_id' => $category->id,
            'name' => 'Producto test',
            'price' => 1000,
            'is_active' => true,
        ]);
    }

    private function createIngredient(BusinessProfile $businessProfile, string $name, float $unitCost): Ingredient
    {
        return Ingredient::create([
            'business_profile_id' => $businessProfile->id,
            'name' => $name,
            'unit_measure' => 'kg',
            'unit_cost' => $unitCost,
        ]);
    }
}
