<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ProductCostService;

class RecipeController extends Controller
{
    /**
     * Show the recipe constructor (ingredients association) for a product.
     */
    public function edit(Product $product)
    {
        $this->authorizeSellerProduct($product);

        $ingredients = Ingredient::where('business_profile_id', Auth::user()->businessProfile->id)->get();
        $product->load('ingredients');

        return view('recipes.edit', compact('product', 'ingredients'));
    }

    /**
     * Update the recipe for a product.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeSellerProduct($product);

        $validated = $request->validate([
            'ingredients' => 'array',
            'ingredients.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
        ]);

        $recipe = collect($validated['ingredients'] ?? [])
            ->filter(fn ($row) => ! empty($row['ingredient_id']) && (float) ($row['quantity'] ?? 0) > 0)
            ->mapWithKeys(fn ($row) => [
                $row['ingredient_id'] => ['quantity' => $row['quantity']],
            ])
            ->all();

        $product->ingredients()->sync($recipe);
        app(ProductCostService::class)->update($product);

        return redirect()->route('products.index')->with('success', 'Receta actualizada. Costos recalculados automáticamente.');
    }

    private function authorizeSellerProduct(Product $product): void
    {
        abort_if($product->business_profile_id !== Auth::user()->businessProfile?->id, 403);
    }
}
