<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $businessProfile = Auth::user()->businessProfile;

        abort_if(! $businessProfile, 403);

        $validated = $request->validate($this->rules());

        Ingredient::create([
            'business_profile_id' => $businessProfile->id,
            ...$validated,
        ]);

        return redirect()->route('ingredients.index')->with('success', 'Ingrediente creado con éxito.');
    }

    /**
     * Show the form for editing the specified ingredient.
     */
    public function edit(Ingredient $ingredient)
    {
        $this->authorizeSellerIngredient($ingredient);

        return view('ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified ingredient in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorizeSellerIngredient($ingredient);

        $ingredient->update($request->validate($this->rules()));

        return redirect()->route('ingredients.index')->with('success', 'Ingrediente actualizado con éxito.');
    }

    /**
     * Remove the specified ingredient from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $this->authorizeSellerIngredient($ingredient);

        $ingredient->delete();

        return redirect()->route('ingredients.index')->with('success', 'Ingrediente eliminado con éxito.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'unit_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
        ];
    }

    private function authorizeSellerIngredient(Ingredient $ingredient): void
    {
        abort_if($ingredient->business_profile_id !== Auth::user()->businessProfile?->id, 403);
    }
}
