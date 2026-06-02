<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $profile = Auth::user()->businessProfile;
        return view('business_profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user    = Auth::user();
        $profile = $user->businessProfile;

        if (!$profile) {
            $profile = new BusinessProfile();
            $profile->user_id = $user->id;
        }

        $profile->business_name = $validated['business_name'];
        $profile->description   = $validated['description'] ?? $profile->description;
        $profile->phone         = $validated['phone'] ?? $profile->phone;
        $profile->address       = $validated['address'] ?? $profile->address;

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $profile->logo = $request->file('logo')->store('logos', 'public');
        }

        $profile->save();

        return redirect()->route('business_profile.edit')
                         ->with('success', 'Perfil actualizado correctamente.');
    }
}