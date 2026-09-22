<?php

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeocodeAddress
{
    /**
     * @return array{lat: float, lng: float}|null
     */
    public function __invoke(string $address): ?array
    {
        $address = trim($address);

        if ($address === '') {
            return null;
        }

        $cacheKey = 'geocode:'.hash('sha256', mb_strtolower($address));

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'Norix Security').' Staff Portal (contact: '.config('norix.email', 'ops@norix.local').')',
                    'Accept' => 'application/json',
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'gb',
                ]);

            if (! $response->successful()) {
                throw new RuntimeException('Geocoding service unavailable.');
            }

            $result = $response->json('0');

            if (! is_array($result) || ! isset($result['lat'], $result['lon'])) {
                return null;
            }

            return [
                'lat' => (float) $result['lat'],
                'lng' => (float) $result['lon'],
            ];
        });
    }
}
