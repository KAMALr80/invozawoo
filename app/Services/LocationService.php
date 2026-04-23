<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LocationService
{
    private $osrmUrl = 'https://router.project-osrm.org'; // Free OSRM
    private $nominatimUrl = 'https://nominatim.openstreetmap.org'; // Free Geocoding

    /**
     * Search locations using Nominatim
     */
    public function searchLocations($query, $limit = 8)
    {
        // Cache results for 24 hours to reduce API calls
        $cacheKey = 'location_search_' . md5($query . $limit);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($query, $limit) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'INVOZA-ERP/1.0 (contact@yourdomain.com)',
                    'Accept' => 'application/json'
                ])->get($this->nominatimUrl . '/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => $limit,
                    'addressdetails' => 1,
                    'countrycodes' => 'in', // India only
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (\Exception $e) {
                Log::error('Location search failed: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get location details by lat/lng
     */
    public function reverseGeocode($lat, $lng)
    {
        $cacheKey = 'reverse_' . $lat . '_' . $lng;

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lng) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'INVOZA-ERP/1.0 (contact@yourdomain.com)',
                ])->get($this->nominatimUrl . '/reverse', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'json',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Reverse geocoding failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Calculate route between points using OSRM
     */
    public function calculateRoute($coordinates)
    {
        if (count($coordinates) < 2) {
            return null;
        }

        // Format: lon,lat;lon,lat
        $points = implode(';', array_map(function($coord) {
            return $coord['lng'] . ',' . $coord['lat'];
        }, $coordinates));

        try {
            $response = Http::get($this->osrmUrl . '/route/v1/driving/' . $points, [
                'overview' => 'full',
                'geometries' => 'geojson',
                'steps' => true
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'distance' => $data['routes'][0]['distance'] / 1000, // km
                    'duration' => $data['routes'][0]['duration'] / 60, // minutes
                    'geometry' => $data['routes'][0]['geometry'],
                    'waypoints' => $data['waypoints']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Route calculation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get distance matrix for multiple points
     */
    public function getDistanceMatrix($coordinates)
    {
        $points = implode(';', array_map(function($coord) {
            return $coord['lng'] . ',' . $coord['lat'];
        }, $coordinates));

        try {
            $response = Http::get($this->osrmUrl . '/table/v1/driving/' . $points);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Distance matrix failed: ' . $e->getMessage());
            return null;
        }
    }
}
