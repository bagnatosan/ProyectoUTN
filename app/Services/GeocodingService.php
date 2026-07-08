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

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $httpRequest = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => config('app.name') . ' (' . (config('app.url') ?: 'http://localhost') . ')',
            ]);

        // WORKAROUND: en entornos Windows locales, PHP/OpenSSL puede fallar
        // al verificar la cadena de certificados de nominatim.openstreetmap.org
        // (cURL error 60 / certificate verify failed), aunque el navegador
        // conecte sin problema usando el almacén nativo de Windows.
        // Se detecta el entorno local por APP_URL en lugar de APP_ENV,
        // así funciona aunque APP_ENV=production (para evitar el warning
        // de migrate:fresh al correr migraciones en desarrollo).
        $appUrl = config('app.url', '');
        $isLocalEnv = app()->environment('local')
            || str_contains($appUrl, '127.0.0.1')
            || str_contains($appUrl, 'localhost');

        if ($isLocalEnv) {
            $httpRequest = $httpRequest->withOptions(['verify' => false]);
        }

        try {
            $response = $httpRequest->get('https://nominatim.openstreetmap.org/search', [
                'q'      => $address . ', Argentina',
                'format' => 'json',
                'limit'  => 1,
            ]);
        } catch (\Exception $e) {
            // Si el geocoding falla (sin internet, SSL, timeout), el registro
            // igual se completa — las coordenadas quedan en null.
            \Illuminate\Support\Facades\Log::warning(
                'GeocodingService: fallo al conectar con Nominatim. ' . $e->getMessage()
            );
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $results = $response->json();
        if (empty($results[0]['lat']) || empty($results[0]['lon'])) {
            return null;
        }

        $coords = [
            'latitude'  => (float) $results[0]['lat'],
            'longitude' => (float) $results[0]['lon'],
        ];

        // Solo cachear resultados exitosos para no bloquear reintentos ante fallos transitorios
        Cache::put($cacheKey, $coords, now()->addDays(30));

        return $coords;
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