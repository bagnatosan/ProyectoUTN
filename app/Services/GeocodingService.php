<?php

namespace App\Services;

use App\Models\BusinessProfile;
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
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => config('app.name') . ' (' . (config('app.url') ?: 'http://localhost') . ')',
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
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

    public function syncProfileCoordinates(
        BusinessProfile $profile,
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
