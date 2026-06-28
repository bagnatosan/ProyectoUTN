<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Services\GeocodingService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $businessCount = BusinessProfile::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

        // Centramos el mapa en la ubicación del usuario logueado.
        // Si es vendedor, usamos la ubicación de su negocio.
        // Si es cliente, usamos su propia ubicación.
        $userLocation = null;

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'seller' && $user->businessProfile) {
                $bp = $user->businessProfile;
                if ($bp->latitude !== null && $bp->longitude !== null) {
                    $userLocation = [
                        'lat' => (float) $bp->latitude,
                        'lng' => (float) $bp->longitude,
                    ];
                }
            } else {
                $clientProfile = $user->clientProfile ?? null;
                if ($clientProfile && $clientProfile->latitude !== null && $clientProfile->longitude !== null) {
                    $userLocation = [
                        'lat' => (float) $clientProfile->latitude,
                        'lng' => (float) $clientProfile->longitude,
                    ];
                }
            }
        }

        return view('map.index', compact('businessCount', 'userLocation'));
    }

    public function markers()
    {
        $businesses = BusinessProfile::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('business_name')
            ->get(['id', 'business_name', 'description', 'address', 'latitude', 'longitude']);

        return response()->json($businesses->map(fn ($business) => [
            'id' => $business->id,
            'name' => $business->business_name,
            'description' => $business->description,
            'address' => $business->address,
            'lat' => (float) $business->latitude,
            'lng' => (float) $business->longitude,
            'catalog_url' => route('catalog.show', $business->id),
        ]));
    }

    public function geocode(Request $request, GeocodingService $geocodingService)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $coords = $geocodingService->geocode($validated['address']);

        if (!$coords) {
            return response()->json(['message' => 'No se encontró la dirección.'], 404);
        }

        return response()->json($coords);
    }
}