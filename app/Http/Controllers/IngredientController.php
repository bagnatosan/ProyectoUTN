<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Muestra el catálogo de materias primas del vendedor logueado.
     */
    public function index(Request $request)
    {
        $businessProfileId = auth()->user()->businessProfile?->id;

        $search = $request->input('search', '');

        $ingredients = Ingredient::where('business_profile_id', $businessProfileId)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('ingredients.index', compact('ingredients', 'search'));
    }

    /**
     * Muestra el formulario para crear un ingrediente nuevo.
     */
    public function create()
    {
        return view('ingredients.create');
    }

    /**
     * Guarda un nuevo ingrediente en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'unit_measure'   => 'required|string|max:50',
            'unit_cost'      => 'required|numeric|min:0',
            'supplier_notes' => 'nullable|string',
            'stock'          => 'nullable|numeric|min:0',
            'stock_minimo'   => 'nullable|numeric|min:0',
        ]);

        $businessProfileId = auth()->user()->businessProfile?->id;

        if (!$businessProfileId) {
            return redirect()->back()->with('error', 'No se encontró un perfil de negocio asociado a tu cuenta.');
        }

        $validated['business_profile_id'] = $businessProfileId;

        Ingredient::create($validated);

        // Si vino un redirect_to (ej: desde el modal del módulo de costos), volvemos ahí
        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))
                ->with('success', '¡Ingrediente guardado con éxito!');
        }

        return redirect()->route('ingredients.index')->with('success', '¡Ingrediente guardado con éxito!');
    }

    /**
     * Muestra el formulario de edición de un ingrediente.
     */
    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $this->authorizeOwnership($ingredient);

        return view('ingredients.edit', compact('ingredient'));
    }

    /**
     * Actualiza los datos de un ingrediente existente.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorizeOwnership($ingredient);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'unit_measure'   => 'required|string|max:50',
            'unit_cost'      => 'required|numeric|min:0',
            'supplier_notes' => 'nullable|string',
            'stock'          => 'nullable|numeric|min:0',
            'stock_minimo'   => 'nullable|numeric|min:0',
        ]);

        $ingredient->update($validated);

        return redirect()->route('ingredients.index')
            ->with('success', 'Materia prima actualizada correctamente.');
    }

    /**
     * Elimina un ingrediente del catálogo.
     */
    public function destroy($ingredient)
    {
        $id = is_object($ingredient) ? $ingredient->id : $ingredient;
        $materiaPrima = Ingredient::findOrFail($id);

        $this->authorizeOwnership($materiaPrima);

        $materiaPrima->delete();

        return redirect()->route('ingredients.index')
            ->with('success', '¡Materia prima eliminada correctamente!');
    }

    /**
     * Verifica que el ingrediente pertenezca al negocio del usuario logueado.
     * Evita que un vendedor edite o borre ingredientes de otro.
     */
    private function authorizeOwnership(Ingredient $ingredient): void
    {
        $businessProfileId = auth()->user()->businessProfile?->id;

        if ($ingredient->business_profile_id !== $businessProfileId) {
            abort(403, 'No tenés permiso para modificar este ingrediente.');
        }
    }
}