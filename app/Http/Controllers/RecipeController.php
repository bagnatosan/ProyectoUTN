<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
   
    public function edit($id)
{
    
    $product = \App\Models\Product::with('ingredients')->findOrFail($id);
    $ingredients = \App\Models\Ingredient::all();
    $allProducts = \App\Models\Product::all();
    $estilosAmigo = ""; 

    
    return view('costos', compact('product', 'ingredients', 'allProducts', 'estilosAmigo'));
    
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

public function addIngredient(Request $request, $recipeId)
{
    // 1. Validamos que vengan tanto el ingrediente como la cantidad
    $request->validate([
        'ingredient_id' => 'required',
        'quantity'      => 'required|numeric|min:0.001'
    ]);

    // 2. Insertamos el cruce con la cantidad real enviada desde el formulario
    \DB::table('product_ingredients')->insert([
        'product_id'    => (int)$recipeId,
        'ingredient_id' => (int)$request->ingredient_id,
        'quantity'      => (float)$request->quantity, // <--- ¡Acá se vuelve dinámico!
        'created_at'    => now(),
        'updated_at'    => now()
    ]);

    return redirect('/recipes/' . $recipeId . '/edit')->with('success', '¡Ingrediente añadido a la receta con éxito!');
}

public function destroy($id)
{
    // 1. Buscamos el registro en la tabla intermedia (pivot)
    // Nota: Si tus compañeros usaron un modelo intermedio llamado RecipeIngredient:
    $relation = \App\Models\RecipeIngredient::find($id);

    // 2. Si no existe como modelo propio, lo buscamos de forma directa en la base de datos de SQLite
    if (!$relation) {
        \DB::table('recipe_ingredient')->where('id', $id)->delete();
    } else {
        $relation->delete();
    }

    // 3. Volvemos al panel de costos premium con un mensaje de éxito
    return redirect('/recipes/1/edit')->with('success', '¡Ingrediente quitado de la receta con éxito!');
}

public function removeIngredient($recipeId, $ingredientId)
{
    // Limpieza absoluta por ID de producto e ingrediente puros, forzando a entero por las dudas
    \DB::table('product_ingredients')
        ->where('product_id', (int)$recipeId)
        ->where('ingredient_id', (int)$ingredientId)
        ->delete();

    // Recargamos la página limpiando la caché de la vista
    return redirect('/recipes/' . $recipeId . '/edit')
            ->with('success', '¡Ingrediente quitado de la receta con éxito!')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
}
}