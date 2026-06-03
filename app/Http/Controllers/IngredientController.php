<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Display a listing of the ingredients.
     */
    public function index()
    {
        return view('ingredients.index');
    }

    /**
     * Show the form for creating a new ingredient.
     */
    public function create()
    {
        return view('ingredients.create');
    }

    /**
     * Store a newly created ingredient in storage.
     */
    public function store(Request $request)
    {
        // Store ingredient logic will be implemented here
        return redirect()->route('ingredients.index')->with('success', 'Ingrediente creado (borrador).');
    }

    /**
     * Show the form for editing the specified ingredient.
     */
    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified ingredient in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        // Update ingredient logic will be implemented here
        return redirect()->route('ingredients.index')->with('success', 'Ingrediente actualizado (borrador).');
    }

    /**
     * Remove the specified ingredient from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        // Delete ingredient logic will be implemented here
        return redirect()->route('ingredients.index')->with('success', 'Ingrediente eliminado (borrador).');
    }
}
