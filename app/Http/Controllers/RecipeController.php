<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use App\Helpers\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $firstProduct = auth()->user()->businessProfile?->products()->first();

        if ($firstProduct) {
            return redirect()->route('recipes.edit', $firstProduct->id);
        }

        return redirect()->route('products.index')
            ->with('info', 'Por favor, crea tu primer producto para poder acceder al módulo de costos.');
    }

    public function edit($id)
    {
        $product = Product::with('ingredients')->findOrFail($id);
        $ingredients = Ingredient::where('business_profile_id', auth()->user()->businessProfile->id)->get();
        $allProducts = Product::where('business_profile_id', auth()->user()->businessProfile->id)->get();

        return view('costos', compact('product', 'ingredients', 'allProducts'));
    }

    /**
     * Devuelve las unidades válidas para un ingrediente (AJAX).
     * El frontend usa esto para filtrar el desplegable de unidad según el ingrediente elegido.
     */
    public function validUnits($ingredientId)
    {
        $ingredient = Ingredient::findOrFail($ingredientId);
        $units = UnitConverter::compatibleUnits($ingredient->unit_measure);

        return response()->json(['units' => $units]);
    }

    /**
     * Agrega un ingrediente a la receta de un producto, con su cantidad y unidad de uso.
     */
    public function addIngredient(Request $request, $recipeId)
    {
        $request->validate([
            'ingredient_id'  => 'required|exists:ingredients,id',
            'quantity'       => 'required|numeric|min:0.001',
            'quantity_unit'  => 'required|string',
        ]);

        $ingredient = Ingredient::findOrFail($request->ingredient_id);

        // Validamos que la unidad elegida sea compatible con la unidad de compra del ingrediente
        if (!UnitConverter::areCompatible($ingredient->unit_measure, $request->quantity_unit)) {
            return redirect('/recipes/' . $recipeId . '/edit')
                ->with('error', 'La unidad seleccionada no es válida para este ingrediente.');
        }

        DB::table('product_ingredients')->insert([
            'product_id'    => (int) $recipeId,
            'ingredient_id' => (int) $request->ingredient_id,
            'quantity'      => (float) $request->quantity,
            'quantity_unit' => $request->quantity_unit,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->recalculateProductCost($recipeId);

        return redirect('/recipes/' . $recipeId . '/edit')
            ->with('success', '¡Ingrediente añadido a la receta con éxito!');
    }

    /**
     * Quita un ingrediente de la receta de un producto.
     */
    public function removeIngredient($recipeId, $ingredientId)
    {
        DB::table('product_ingredients')
            ->where('product_id', (int) $recipeId)
            ->where('ingredient_id', (int) $ingredientId)
            ->delete();

        $this->recalculateProductCost($recipeId);

        return redirect('/recipes/' . $recipeId . '/edit')
            ->with('success', '¡Ingrediente quitado de la receta con éxito!');
    }

    /**
     * Recalcula el costo estimado y el precio sugerido de un producto,
     * convirtiendo cada cantidad de receta a la unidad de compra del ingrediente.
     * El margen usado es: el del producto si está definido, sino el del negocio.
     */
    private function recalculateProductCost($productId)
    {
        $product = Product::with('businessProfile')->findOrFail($productId);
        $totalCost = 0;

        foreach ($product->ingredients as $ingredient) {
            $quantity = (float) $ingredient->pivot->quantity;
            $quantityUnit = $ingredient->pivot->quantity_unit ?? $ingredient->unit_measure;

            // Convertimos la cantidad usada en la receta a la unidad de compra del ingrediente
            $quantityInPurchaseUnit = UnitConverter::convert(
                $quantity,
                $quantityUnit,
                $ingredient->unit_measure
            );

            $totalCost += $ingredient->unit_cost * $quantityInPurchaseUnit;
        }

        // Margen: el del producto si está definido, sino el del negocio, sino 3 por defecto
        $margin = $product->custom_margin
            ?? $product->businessProfile->profit_margin
            ?? 3;

        $product->estimated_cost = $totalCost;
        $product->suggested_price = $totalCost * $margin;
        $product->save();
    }
}