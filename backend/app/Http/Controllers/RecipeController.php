<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * 1. Guardar o actualizar los ingredientes de un producto (La Receta)
     */
    public function updateRecipe(Request $request, $productId)
    {
        // Buscamos el producto (plato) al que le queremos armar la receta
        $product = Product::findOrFail($productId);

        // Validamos que lo que venga del formulario sea un array de ingredientes y cantidades
        $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
        ]);

        // Preparamos los datos para la tabla intermedia (ID del ingrediente => ['quantity' => cantidad])
        $syncData = [];
        foreach ($request->ingredients as $item) {
            $syncData[$item['id']] = ['quantity' => $item['quantity']];
        }

        // El método sync() es mágico: borra los ingredientes viejos de este producto 
        // y guarda los nuevos con sus cantidades en la tabla product_ingredients en un solo paso.
        $product->ingredients()->sync($syncData);

        return redirect()->back()->with('success', 'Receta guardada y actualizada con éxito.');
    }

    /**
     * 2. Limpiar por completo la receta de un producto (Desasociar todo)
     */
    public function clearRecipe($productId)
    {
        $product = Product::findOrFail($productId);

        // El método detach() vacía por completo la receta de este producto en la tabla intermedia
        $product->ingredients()->detach();

        return redirect()->back()->with('success', 'Se han removido todos los ingredientes de la receta.');
    }
}