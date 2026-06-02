<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    // 1. Mostrar el listado de todos los ingredientes
    public function index()
    {
        $ingredients = Ingredient::all(); // Le pide todos los ingredientes a la base de datos
        return view('ingredients.index', compact('ingredients')); // Te manda a la pantalla visual
    }

    // 2. Mostrar el formulario para crear un ingrediente nuevo
    public function create()
    {
        return view('ingredients.create'); // Te manda a la pantalla del formulario
    }

    // 3. Guardar el nuevo ingrediente en la base de datos
    public function store(Request $request)
    {
        // Validamos que el usuario cargue los datos obligatorios y correspondan
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string',
            'cost_per_unit' => 'required|numeric|min:0',
        ]);

        // Guardamos en la base de datos usando el modelo seguro
        Ingredient::create($request->all());

        // Redirecciona al listado con un cartel de éxito
        return redirect()->route('ingredients.index')->with('success', 'Ingrediente creado con éxito.');
    }

    // 4. Mostrar el formulario con los datos de un ingrediente para editarlo
    public function edit(string $id)
    {
        // Buscamos el ingrediente por su ID en la base de datos. Si no lo encuentra, tira error 404.
        $ingredient = Ingredient::findOrFail($id); 
        
        // Te manda a la vista pasándole el ingrediente encontrado
        return view('ingredients.edit', compact('ingredient'));
    }

    // 5. Procesar los cambios del formulario y actualizarlos en la base de datos
    public function update(Request $request, string $id)
    {
        // Validamos que los datos que vienen modificados sigan cumpliendo las reglas de negocio
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string',
            'cost_per_unit' => 'required|numeric|min:0',
        ]);

        // Buscamos el ingrediente original
        $ingredient = Ingredient::findOrFail($id);

        // Impactamos los cambios nuevos en la base de datos
        $ingredient->update($request->all());

        // Volvemos al listado avisando que se actualizó con éxito
        return redirect()->route('ingredients.index')->with('success', 'Ingrediente actualizado con éxito.');
    }

    // 6. Eliminar un ingrediente de la base de datos
    public function destroy(string $id)
    {
        // Buscamos el ingrediente que se quiere borrar
        $ingredient = Ingredient::findOrFail($id);

        // Lo eliminamos físicamente de la tabla de MySQL
        $ingredient->delete();

        // Volvemos al listado avisando que ya no existe más
        return redirect()->route('ingredients.index')->with('success', 'Ingrediente eliminado correctamente.');
    }
}