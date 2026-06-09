<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IngredientController extends Controller
{
    /**
     * Muestra el panel unificado de costos e ingredientes.
     */
    public function index()
    {
        // 1. Traemos todos los ingredientes reales
        $ingredients = Ingredient::all();

        // 2. Traemos el CSS global para mantener el modo oscuro global
        $cssPath = resource_path('css/app.css');
        $estilosAmigo = '';
        if (File::exists($cssPath)) {
            $estilosAmigo = File::get($cssPath);
        }

        // Retornamos TU vista espectacular
        return view('costos', compact('ingredients', 'estilosAmigo'));
    }

    /**
     * Guarda un nuevo ingrediente en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        // 💡 SOLUCIÓN: Buscamos el ID del negocio del usuario logueado.
        // Si no hay nadie logueado (porque estamos testeando), le clavamos el ID 1 por defecto.
        $validated['business_profile_id'] = auth()->user()->business_profile_id ?? 1;

        \App\Models\Ingredient::create($validated);

        // Redirecciona al panel con mensaje de éxito
        return redirect()->route('ingredients.index')->with('success', '¡Ingrediente añadido con éxito!');
    }

    /**
     * Actualiza el costo o los datos de un ingrediente.
     * Al ejecutarse esto, los Observers de tu amiga recalculan los productos automáticamente.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        // Aseguramos que mantenga el perfil si no estaba seteado
        if (!$ingredient->business_profile_id) {
            $validated['business_profile_id'] = auth()->user()->business_profile_id ?? 1;
        }

        $ingredient->update($validated);

        return redirect()->route('ingredients.index')->with('success', '¡Materia prima actualizada y costos recalculados!');
    }

    /**
     * Elimina un ingrediente del inventario.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return redirect()->route('ingredients.index')->with('success', 'Ingrediente eliminado correctamente.');
    }
}