<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessProfileController extends Controller
{
    /**
     * Show the form for editing the business profile.
     */
    public function edit()
    {
        $profile = Auth::user()->businessProfile;
        return view('business_profile.edit', compact('profile'));
    }

    /**
     * Update the business profile in storage.
     */
    public function update(Request $request)
    {
        // Validation and update logic will be implemented here
        return redirect()->back()->with('success', 'Perfil de negocio actualizado (borrador).');
    }
}
