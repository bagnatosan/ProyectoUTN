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
        $request->merge([
            'phone' => $request->has('phone') ? trim($request->phone) : null
        ]);

        $validated = $request->validate([
            'business_name'       => 'required|string|max:255',
            'description'         => 'nullable|string',
            'phone'               => ['nullable', 'string', 'max:20', 'regex:/^\+54\d+$/'],
            'address'             => 'nullable|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover_image'         => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'remove_logo'         => 'nullable|boolean',
            'remove_cover_image'  => 'nullable|boolean',
            'profit_margin'       => 'nullable|numeric|min:1|max:50',
            'bank_cbu'            => 'nullable|string|max:22',
            'bank_alias'          => 'nullable|string|max:100',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:255',
            'shipping_cost'       => 'nullable|numeric|min:0',
            'mp_public_key'       => 'nullable|string|max:255',
            'mp_access_token'     => 'nullable|string|max:255',
        ], [
            'phone.regex' => 'El teléfono debe comenzar con +54 y no debe contener espacios.',
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

        $profile->business_name       = $validated['business_name'];
        $profile->description         = $validated['description'] ?? $profile->description;
        $profile->phone               = $validated['phone'] ?? $profile->phone;
        $profile->address             = $validated['address'] ?? null;
        $profile->profit_margin       = $validated['profit_margin'] ?? $profile->profit_margin ?? 3;
        if ($request->has('bank_cbu')) { $profile->bank_cbu = $validated['bank_cbu'] ?? null; }
        if ($request->has('bank_alias')) { $profile->bank_alias = $validated['bank_alias'] ?? null; }
        if ($request->has('bank_name')) { $profile->bank_name = $validated['bank_name'] ?? null; }
        if ($request->has('bank_account_holder')) { $profile->bank_account_holder = $validated['bank_account_holder'] ?? null; }
        $profile->shipping_cost       = $validated['shipping_cost'] ?? 0;
        $profile->mp_public_key       = $validated['mp_public_key'] ?? null;
        $profile->mp_access_token     = $validated['mp_access_token'] ?? null;

        $latitude  = isset($validated['latitude'])  ? (float) $validated['latitude']  : null;
        $longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;
        $addressChanged = $previousAddress !== $profile->address;

        if ($latitude !== null && $longitude !== null) {
            $profile->latitude  = $latitude;
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

        if ($request->boolean('remove_logo') && !$request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('r2')->delete($profile->logo);
            }
            $profile->logo = null;
        } elseif ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('r2')->delete($profile->logo);
            }
            $stored = $request->file('logo')->store('logos', 'r2');
            if ($stored) $profile->logo = $stored;
        }

        if ($request->boolean('remove_cover_image') && !$request->hasFile('cover_image')) {
            if ($profile->cover_image) {
                Storage::disk('r2')->delete($profile->cover_image);
            }
            $profile->cover_image = null;
        } elseif ($request->hasFile('cover_image')) {
            if ($profile->cover_image) {
                Storage::disk('r2')->delete($profile->cover_image);
            }
            $stored = $request->file('cover_image')->store('covers', 'r2');
            if ($stored) $profile->cover_image = $stored;
        }

        $profile->save();

        return redirect()->route('business_profile.edit')
                         ->with('success', 'Perfil actualizado correctamente.');
    }

    public function testMpCredentials(Request $request)
    {
        $token = $request->input('mp_access_token');
        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'El token está vacío.'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->get('https://api.mercadopago.com/users/me');

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => '¡Conexión exitosa! Las credenciales son válidas.']);
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return response()->json(['success' => false, 'message' => 'El token ingresado no es válido o expiró.'], 422);
    }

    public function mercadopagoConnect(Request $request)
    {
        $clientId = env('MERCADOPAGO_CLIENT_ID', '8765432101234567');
        if ($clientId === '8765432101234567') {
            return redirect()->route('business_profile.mercadopago.callback', ['code' => 'mock_code_123']);
        }
        $redirectUri = route('business_profile.mercadopago.callback');
        $url = "https://auth.mercadopago.com/authorization?client_id={$clientId}&response_type=code&platform_id=mp&redirect_uri=" . urlencode($redirectUri);
        return redirect()->away($url);
    }

    public function mercadopagoCallback(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('business_profile.edit')->with('error', 'No se recibió el código de autorización.');
        }

        $clientId = env('MERCADOPAGO_CLIENT_ID', '8765432101234567');
        $clientSecret = env('MERCADOPAGO_CLIENT_SECRET', 'test_client_secret');

        try {
            $response = \Illuminate\Support\Facades\Http::post('https://api.mercadopago.com/oauth/token', [
                'client_secret' => $clientSecret,
                'client_id' => $clientId,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => route('business_profile.mercadopago.callback'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $profile = Auth::user()->businessProfile;
                if (!$profile) {
                    $profile = new BusinessProfile(['user_id' => Auth::id()]);
                }
                $profile->mp_access_token = $data['access_token'];
                $profile->mp_public_key = $data['public_key'] ?? null;
                $profile->save();

                return redirect()->route('business_profile.edit')->with('success', '¡Cuenta de Mercado Pago vinculada correctamente!');
            }
        } catch (\Exception $e) {
            //
        }

        if ($clientId === '8765432101234567') {
            $profile = Auth::user()->businessProfile;
            if ($profile) {
                $profile->mp_access_token = 'APP_USR-MOCK-ACCESS-TOKEN-1234';
                $profile->mp_public_key = 'APP_USR-MOCK-PUBLIC-KEY-1234';
                $profile->save();
                return redirect()->route('business_profile.edit')->with('success', '¡Vinculación de prueba (Mock Sandbox) exitosa!');
            }
        }

        return redirect()->route('business_profile.edit')->with('error', 'Error al conectar con Mercado Pago.');
    }
}
