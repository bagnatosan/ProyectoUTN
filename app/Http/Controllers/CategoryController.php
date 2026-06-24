<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $businessProfileId = auth()->user()->businessProfile?->id;

        $categories = Category::where('business_profile_id', $businessProfileId)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeOwnership($category);

        $request->validate(['name' => 'required|max:50']);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoria actualizada con exito',
            'category' => $category
        ]);
    }

    public function create(Request $request)
    {
        $request->validate(['name' => 'required|max:50']);

        $businessProfileId = auth()->user()->businessProfile?->id;

        if (!$businessProfileId) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un perfil de negocio asociado a tu cuenta.'
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'business_profile_id' => $businessProfileId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoria creada con exito',
            'category' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        $this->authorizeOwnership($category);

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoria eliminada con exito.'
        ]);
    }

    /**
     * Verifica que la categoría pertenezca al negocio del usuario logueado.
     * Evita que un vendedor edite o borre categorías de otro.
     */
    private function authorizeOwnership(Category $category): void
    {
        $businessProfileId = auth()->user()->businessProfile?->id;

        if ($category->business_profile_id !== $businessProfileId) {
            abort(403, 'No tenés permiso para modificar esta categoría.');
        }
    }
}