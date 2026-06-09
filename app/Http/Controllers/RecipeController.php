<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
   
    public function edit($productId)
    {
        $product = Product::with('ingredients')->findOrFail($productId);
        $ingredients = Ingredient::all(); // Materias primas disponibles a la izquierda
        
        
        $cssPath = resource_path('css/app.css');
        $estilosAmigo = '';
        if (\Illuminate\Support\Facades\File::exists($cssPath)) {
            $estilosAmigo = \Illuminate\Support\Facades\File::get($cssPath);
        }

        // Retorna la vista con todo lo que necesita
        return view('costos', compact('product', 'ingredients', 'estilosAmigo'));
    }

    // Guarda o actualiza ingredientes
    public function update(\Illuminate\Http\Request $request, $productId)
{
    // 1. Buscamos el producto (Torta de Chocolate)
    $product = \App\Models\Product::findOrFail($productId);

    // 2. Procesamos los ingredientes que enviaste desde el formulario
    $syncData = [];
    if ($request->has('ingredients')) {
        foreach ($request->input('ingredients') as $item) {
            $syncData[$item['id']] = ['quantity' => $item['quantity']];
        }
    }

    // 3. Guardamos la unión en la tabla intermedia (product_ingredients)
    $product->ingredients()->sync($syncData);

    // 🔥 EL TRUCO MÁGICO: Calculamos el costo real de forma manual acá mismo
    $totalCost = 0;
    
    // Volvemos a leer los ingredientes actualizados directamente de la base de datos con sus costos
    foreach ($product->ingredients()->get() as $ingredient) {
        // Multiplicamos el costo unitario por la cantidad de la tabla pivote
        $totalCost += $ingredient->unit_cost * $ingredient->pivot->quantity;
    }

    // 4. Guardamos los totales reales en el producto para que dejen de estar en 0
    $product->estimated_cost = $totalCost;
    $product->suggested_price = $totalCost * 3; // Margen del 300% (Multiplicador x3)
    $product->save();

    // 5. Redireccionamos de nuevo a tu pantalla modo oscuro con el mensaje de éxito
    return redirect()->route('recipes.edit', $product->id)
                     ->with('success', '¡Receta actualizada y costos recalculados correctamente!');
}
}