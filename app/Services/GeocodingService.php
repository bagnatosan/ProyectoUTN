<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function geocode(?string $address): ?array
    {
        if (blank($address)) {
            return null;
        }

        $normalized = mb_strtolower(trim($address));
        $cacheKey = 'geocode:' . md5($normalized);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
            $request = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => config('app.name') . ' (' . (config('app.url') ?: 'http://localhost') . ')',
                ]);

            // WORKAROUND: en entornos Windows locales, PHP/OpenSSL puede fallar
            // al verificar la cadena de certificados de nominatim.openstreetmap.org
            // (cURL error 60 / certificate verify failed), aunque el navegador
            // conecte sin problema usando el almacén nativo de Windows.
            // Esto NO afecta producción: solo se desactiva la verificación
            // cuando APP_ENV=local. En servidores Linux (producción) este
            // problema no ocurre.
            if (app()->environment('local')) {
                $request = $request->withOptions(['verify' => false]);
            }

            $response = $request->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address . ', Argentina',
                'format' => 'json',
                'limit' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $results = $response->json();
            if (empty($results[0]['lat']) || empty($results[0]['lon'])) {
                return null;
            }

            return [
                'latitude' => (float) $results[0]['lat'],
                'longitude' => (float) $results[0]['lon'],
            ];
        });
    }

    /**
     * Geocodifica una dirección y asigna latitude/longitude al modelo recibido.
     * Funciona con cualquier modelo Eloquent que tenga los campos
     * 'address', 'latitude' y 'longitude' (BusinessProfile, ClientProfile, etc.).
     */
    public function syncProfileCoordinates(
        Model $profile,
        ?string $address,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $forceGeocode = false
    ): void {
        if ($latitude !== null && $longitude !== null) {
            $profile->latitude = $latitude;
            $profile->longitude = $longitude;
            return;
        }

        if (blank($address)) {
            $profile->latitude = null;
            $profile->longitude = null;
            return;
        }

        $addressChanged = $profile->isDirty('address') || $forceGeocode;

        if ($addressChanged || $profile->latitude === null || $profile->longitude === null) {
            $coords = $this->geocode($address);
            if ($coords) {
                $profile->latitude = $coords['latitude'];
                $profile->longitude = $coords['longitude'];
            }
        }
    }
}