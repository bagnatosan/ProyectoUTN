<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        return view('categories.index');
    }

    public function update()
    {
        
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        // Store category logic will be implemented here
        return redirect()->route('categories.index')->with('success', 'Categoría creada (borrador).');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Delete category logic will be implemented here
        return redirect()->route('categories.index')->with('success', 'Categoría eliminada (borrador).');
    }
}
