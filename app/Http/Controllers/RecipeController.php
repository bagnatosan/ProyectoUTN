<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Show the recipe constructor (ingredients association) for a product.
     */
    public function edit(Product $product)
    {
        $ingredients = Ingredient::all();
        return view('recipes.edit', compact('product', 'ingredients'));
    }

    /**
     * Update the recipe for a product.
     */
    public function update(Request $request, Product $product)
    {
        // Association logic between product and ingredients (product_ingredients pivot)
        return redirect()->route('products.index')->with('success', 'Receta del producto actualizada (borrador).');
    }
}
