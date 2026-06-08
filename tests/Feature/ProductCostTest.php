<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductIngredient;
use App\Models\User;
use App\Services\ProductCostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCostTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_cost_service_calculates_estimated_cost_and_suggested_price(): void
    {
        $product = $this->createProduct();
        $flour = $this->createIngredient($product->businessProfile, 'Harina', 1.25);
        $sugar = $this->createIngredient($product->businessProfile, 'Azucar', 0.80);

        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $flour->id,
            'quantity' => 10,
        ]);
        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $sugar->id,
            'quantity' => 5,
        ]);

        $costs = app(ProductCostService::class)->calculate($product);

        $this->assertSame(16.5, $costs['estimated_cost']);
        $this->assertSame(49.5, $costs['suggested_price']);
    }

    public function test_product_costs_are_updated_when_recipe_ingredients_change(): void
    {
        $product = $this->createProduct();
        $ingredient = $this->createIngredient($product->businessProfile, 'Chocolate', 2.50);

        $recipeIngredient = ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 4,
        ]);

        $product->refresh();
        $this->assertSame('10.00', $product->estimated_cost);
        $this->assertSame('30.00', $product->suggested_price);

        $recipeIngredient->update(['quantity' => 6]);

        $product->refresh();
        $this->assertSame('15.00', $product->estimated_cost);
        $this->assertSame('45.00', $product->suggested_price);

        $recipeIngredient->delete();

        $product->refresh();
        $this->assertSame('0.00', $product->estimated_cost);
        $this->assertSame('0.00', $product->suggested_price);
    }

    public function test_product_costs_are_updated_when_ingredient_unit_cost_changes(): void
    {
        $product = $this->createProduct();
        $ingredient = $this->createIngredient($product->businessProfile, 'Manteca', 3.00);

        ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 2,
        ]);

        $ingredient->update(['unit_cost' => 4.25]);

        $product->refresh();
        $this->assertSame('8.50', $product->estimated_cost);
        $this->assertSame('25.50', $product->suggested_price);
    }

    private function createProduct(): Product
    {
        $user = User::factory()->create(['role' => 'seller']);
        $businessProfile = BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Panaderia Test',
            'description' => 'Productos artesanales',
            'phone' => '123456789',
            'logo' => 'https://example.com/logo.png',
        ]);
        $category = Category::create(['name' => 'Panificados']);

        return Product::create([
            'business_profile_id' => $businessProfile->id,
            'category_id' => $category->id,
            'name' => 'Pan casero',
            'description' => 'Pan artesanal',
            'price' => 1000,
            'is_active' => true,
        ]);
    }

    private function createIngredient(BusinessProfile $businessProfile, string $name, float $unitCost): Ingredient
    {
        return Ingredient::create([
            'business_profile_id' => $businessProfile->id,
            'name' => $name,
            'unit_measure' => 'unidad',
            'unit_cost' => $unitCost,
        ]);
    }
}
