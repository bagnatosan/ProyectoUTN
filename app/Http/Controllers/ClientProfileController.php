<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->clientProfile;

        if ($profile && $profile->user_id !== $user->id) {
            abort(403, 'No tenés permiso para editar este perfil.');
        }

        return view('client_profile.edit', compact('profile'));
    }

    public function update(Request $request, GeocodingService $geocodingService)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $profile = $user->clientProfile;

        if ($profile && $profile->user_id !== $user->id) {
            abort(403, 'No tenés permiso para editar este perfil.');
        }

        $user->name = $validated['name'];
        $user->save();

        if (!$profile) {
            $profile = new ClientProfile();
            $profile->user_id = $user->id;
        }

        $profile->address = $validated['address'];

        $geocodingService->syncProfileCoordinates($profile, $profile->address, null, null, $profile->isDirty('address'));

        $profile->save();

        return redirect()->route('client_profile.edit')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'      => 'required|string|min:8',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('client_profile.edit')
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('client_profile.edit')
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}