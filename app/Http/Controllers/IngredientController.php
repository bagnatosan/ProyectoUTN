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
        $validated['business_profile_id'] = auth()->user()->businessProfile?->id ?? 1;

        \App\Models\Ingredient::create($validated);

        // Redirecciona al panel con mensaje de éxito
        //return redirect()->route('ingredients.index')->with('success', '¡Ingrediente añadido con éxito!');
        return redirect('/recipes/1/edit')->with('success', '¡Ingrediente guardado con éxito!');
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
            $validated['business_profile_id'] = auth()->user()->businessProfile?->id ?? 1;
        }

        $ingredient->update($validated);

        //return redirect()->route('ingredients.index')->with('success', '¡Materia prima actualizada y costos recalculados!');
        return redirect()->back()->with('success', 'Materia prima agregada correctamente.');
    }

    // 1. Para mostrar el formulario de "+ Nuevo Ingrediente"
    public function create()
    {
        return view('ingredients.create'); 
        // Nota: Asegurate de tener el archivo resources/views/ingredients/create.blade.php creado
    }

    // 2. Para mostrar el formulario de "✏️ Editar"
    public function edit($id)
    {
        $ingredient = \App\Models\Ingredient::findOrFail($id);
        return view('ingredients.edit', compact('ingredient'));
        // Nota: Asegurate de tener el archivo resources/views/ingredients/edit.blade.php creado
    }

  
public function show($id)
{
    // Si entran acá por un F5, los redirigimos suavemente de vuelta al panel de costos
    return redirect('/recipes/2/edit')->with('info', 'Página recargada con éxito.');
}

    public function destroy($ingredient)
{
    
    $id = is_object($ingredient) ? $ingredient->id : $ingredient;

    // 2. Buscamos el ingrediente de forma segura en SQLite
    $materiaPrima = \App\Models\Ingredient::findOrFail($id);
    
    // 3. Lo borramos de la base de datos
    $materiaPrima->delete();

    // 4. Volvemos al panel de costos con la receta activa actual (ej: la número 2)
    return redirect()->back()->with('success', '¡Materia prima eliminada por completo!');
}
}