<?php
// app/Services/GeocodingService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Convert address to coordinates using Nominatim (OpenStreetMap)
     * COMPLETELY FREE - No API Key Required
     */
    public function geocode($address)
    {
        // Cache results for 30 days to avoid rate limiting
        $cacheKey = 'geocode_' . md5($address);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'ERP-System/1.0 (contact@yourcompany.com)', // Change this!
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1,
                ]);

                if ($response->successful() && count($response->json()) > 0) {
                    $data = $response->json()[0];
                    return [
                        'lat' => (float) $data['lat'],
                        'lng' => (float) $data['lon'],
                        'display_name' => $data['display_name'],
                    ];
                }

                return null;

            } catch (\Exception $e) {
                Log::error('Geocoding failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Reverse geocoding - coordinates to address
     */
    public function reverseGeocode($lat, $lng)
    {
        $cacheKey = 'reverse_' . $lat . '_' . $lng;

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lng) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'ERP-System/1.0 (contact@yourcompany.com)',
                ])->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'json',
                ]);

                if ($response->successful()) {
                    return $response->json()['display_name'] ?? null;
                }

                return null;

            } catch (\Exception $e) {
                Log::error('Reverse geocoding failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Batch geocode multiple addresses (with delay to respect rate limits)
     */
    public function batchGeocode($addresses)
    {
        $results = [];

        foreach ($addresses as $key => $address) {
            $results[$key] = $this->geocode($address);

            // Respect Nominatim's usage policy: max 1 request per second
            if ($key < count($addresses) - 1) {
                sleep(1);
            }
        }

        return $results;
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
