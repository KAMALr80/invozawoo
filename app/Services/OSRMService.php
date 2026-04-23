<?php
// app/Services/OSRMService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OSRMService
{
    /**
     * Get optimized route between multiple points
     * FREE - No API Key Required
     */
    public function getOptimizedRoute($coordinates)
    {
        // Format: lon,lat;lon,lat;lon,lat
        $locations = implode(';', array_map(function($coord) {
            return "{$coord['lng']},{$coord['lat']}";
        }, $coordinates));

        try {
            $response = Http::get("https://router.project-osrm.org/route/v1/driving/{$locations}", [
                'alternatives' => false,
                'steps' => true,
                'geometries' => 'geojson',
                'overview' => 'full',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'distance' => $data['routes'][0]['distance'] / 1000, // km
                    'duration' => $data['routes'][0]['duration'] / 60, // minutes
                    'geometry' => $data['routes'][0]['geometry'],
                    'steps' => $data['routes'][0]['legs'],
                ];
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('OSRM Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get distance matrix for multiple points
     */
    public function getDistanceMatrix($coordinates)
    {
        $locations = implode(';', array_map(function($coord) {
            return "{$coord['lng']},{$coord['lat']}";
        }, $coordinates));

        try {
            $response = Http::get("https://router.project-osrm.org/table/v1/driving/{$locations}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('OSRM Matrix Error: ' . $e->getMessage());
            return null;
        }
    }
}
