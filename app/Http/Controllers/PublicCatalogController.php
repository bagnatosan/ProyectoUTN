<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    /**
     * Display the public catalog of a business.
     */
    public function show($id)
    {
        $business = BusinessProfile::with(['products' => function ($query) {
            $query->where('is_active', true)->with('category');
        }])->findOrFail($id);

        $products = $business->products;
        $categories = $products->pluck('category')->unique('id')->filter();

        return view('catalog.show', compact('business', 'products', 'categories'));
    }
}
