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
        $business = BusinessProfile::findOrFail($id);
        return view('catalog.show', compact('business'));
    }
}
