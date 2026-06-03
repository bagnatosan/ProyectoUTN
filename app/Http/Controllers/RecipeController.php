<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
   
    public function edit($productId)
    {
        $product = Product::findOrFail($productId);
        $ingredients = Ingredient::all(); //aca el usuario elige que reservar
        
        return view('recipes.edit', compact('product', 'ingredients'));
    }

    // Guarda o actualiza ingredientes
    public function update(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Si el usuario borró todos los ingredientes o mandó la receta vacía
        if (!$request->has('ingredients') || empty($request->ingredients)) {
            $product->ingredients()->detach(); // Limpia la receta por completo
            return redirect()->back()->with('success', 'Receta vaciada con éxito.');
        }

        // Si vienen ingredientes, se valida
        $request->validate([
            'ingredients' => 'array',
            'ingredients.*.id' => 'exists:ingredients,id',
            'ingredients.*.quantity' => 'numeric|min:0.01',
        ]);

        // Sincronizamos los ingredientes con sus cantidades en la tabla pivot
        $syncData = [];
        foreach ($request->ingredients as $item) {
            $syncData[$item['id']] = ['quantity' => $item['quantity']];
        }

        $product->ingredients()->sync($syncData);

        return redirect()->back()->with('success', 'Receta actualizada con éxito.');
    }
}