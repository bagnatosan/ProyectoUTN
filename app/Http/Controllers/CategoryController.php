<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        return view('categories.index');
    }

    public function update(Request $request , Category $category)
    {
        $request->validate(['name' => 'required|max:50',
        ]);

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

        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoria creada con exito',
            'category' => $category
        ]);

    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoria eliminada con exito.'
        ]);
        
    }
}
