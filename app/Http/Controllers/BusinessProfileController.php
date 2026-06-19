<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $user    = Auth::user();
        $profile = $user->businessProfile;

        if ($profile && $profile->user_id !== $user->id) {
            abort(403, 'No tenés permiso para editar este perfil.');
        }

        return view('business_profile.edit', compact('profile'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('business_profile.edit')
                            ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('business_profile.edit')
                        ->with('success', 'Contraseña actualizada correctamente.');
    }

    public function update(Request $request, GeocodingService $geocodingService)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user    = Auth::user();
        $profile = $user->businessProfile;

        if ($profile && $profile->user_id !== $user->id) {
            abort(403, 'No tenés permiso para editar este perfil.');
        }

        if (!$profile) {
            $profile = new BusinessProfile();
            $profile->user_id = $user->id;
        }

        $previousAddress = $profile->address;

        $profile->business_name = $validated['business_name'];
        $profile->description   = $validated['description'] ?? $profile->description;
        $profile->phone         = $validated['phone'] ?? $profile->phone;
        $profile->address       = $validated['address'] ?? null;

        $latitude = isset($validated['latitude']) ? (float) $validated['latitude'] : null;
        $longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;
        $addressChanged = $previousAddress !== $profile->address;

        if ($latitude !== null && $longitude !== null) {
            $profile->latitude = $latitude;
            $profile->longitude = $longitude;
        } else {
            $geocodingService->syncProfileCoordinates(
                $profile,
                $profile->address,
                null,
                null,
                $addressChanged
            );
        }

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
