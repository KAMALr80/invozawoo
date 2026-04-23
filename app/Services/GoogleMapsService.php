<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleMapsService
{
    protected $apiKey;
    protected $geocodeUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
    protected $placesUrl = 'https://maps.googleapis.com/maps/api/place';
    protected $directionsUrl = 'https://maps.googleapis.com/maps/api/directions/json';
    protected $distanceMatrixUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /* =========================================================
       1. GEOCODING - Address to Coordinates
    ========================================================= */

    /**
     * Geocode address to get coordinates
     * Uses Geocoding API if available, falls back to Places API
     */
    public function geocodeAddress($address)
    {
        $cacheKey = 'geocode_' . md5($address);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
            try {
                // Try Geocoding API first (if enabled)
                $response = Http::get($this->geocodeUrl, [
                    'address' => $address,
                    'key' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK') {
                        $location = $data['results'][0]['geometry']['location'];
                        return [
                            'lat' => $location['lat'],
                            'lng' => $location['lng'],
                            'formatted_address' => $data['results'][0]['formatted_address'],
                            'place_id' => $data['results'][0]['place_id'] ?? null,
                            'status' => 'OK'
                        ];
                    }
                }

                // Fallback to Places API
                return $this->geocodeViaPlaces($address);

            } catch (\Exception $e) {
                Log::error('Geocoding Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Fallback geocoding using Places API
     */
    public function geocodeViaPlaces($address)
    {
        try {
            $response = Http::get($this->placesUrl . '/findplacefromtext/json', [
                'input' => $address,
                'inputtype' => 'textquery',
                'fields' => 'geometry,formatted_address,place_id,name,address_component',
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['candidates'])) {
                    $place = $data['candidates'][0];
                    $location = $place['geometry']['location'];

                    return [
                        'lat' => $location['lat'],
                        'lng' => $location['lng'],
                        'formatted_address' => $place['formatted_address'],
                        'place_id' => $place['place_id'],
                        'name' => $place['name'] ?? null,
                        'status' => 'OK'
                    ];
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Places Geocoding Error: ' . $e->getMessage());
            return null;
        }
    }

    /* =========================================================
       2. REVERSE GEOCODING - Coordinates to Address
    ========================================================= */

    /**
     * Reverse geocode coordinates to get address
     */
    public function reverseGeocode($lat, $lng)
    {
        $cacheKey = 'reverse_' . $lat . '_' . $lng;

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lng) {
            try {
                $response = Http::get($this->geocodeUrl, [
                    'latlng' => "{$lat},{$lng}",
                    'key' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK' && !empty($data['results'])) {
                        return [
                            'address' => $data['results'][0]['formatted_address'],
                            'place_id' => $data['results'][0]['place_id'],
                            'components' => $this->extractAddressComponents($data['results'][0]['address_components'])
                        ];
                    }
                }
                return null;
            } catch (\Exception $e) {
                Log::error('Reverse Geocoding Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Extract address components from geocoding response
     */
    private function extractAddressComponents($components)
    {
        $result = [
            'street_number' => null,
            'route' => null,
            'locality' => null,
            'city' => null,
            'state' => null,
            'country' => null,
            'postal_code' => null
        ];

        foreach ($components as $component) {
            $types = $component['types'];

            if (in_array('street_number', $types)) {
                $result['street_number'] = $component['long_name'];
            }
            if (in_array('route', $types)) {
                $result['route'] = $component['long_name'];
            }
            if (in_array('locality', $types)) {
                $result['locality'] = $component['long_name'];
                $result['city'] = $component['long_name'];
            }
            if (in_array('administrative_area_level_1', $types)) {
                $result['state'] = $component['long_name'];
            }
            if (in_array('country', $types)) {
                $result['country'] = $component['long_name'];
            }
            if (in_array('postal_code', $types)) {
                $result['postal_code'] = $component['long_name'];
            }
        }

        return $result;
    }

    /* =========================================================
       3. PLACE AUTOCOMPLETE - For Address Search
    ========================================================= */

    /**
     * Autocomplete address input (for search suggestions)
     */
    public function autocompleteAddress($input, $country = 'IN')
    {
        try {
            $response = Http::get($this->placesUrl . '/autocomplete/json', [
                'input' => $input,
                'types' => 'address',
                'components' => "country:{$country}",
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK') {
                    return collect($data['predictions'])->map(function($prediction) {
                        return [
                            'place_id' => $prediction['place_id'],
                            'description' => $prediction['description'],
                            'main_text' => $prediction['structured_formatting']['main_text'],
                            'secondary_text' => $prediction['structured_formatting']['secondary_text']
                        ];
                    })->toArray();
                }
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Autocomplete Error: ' . $e->getMessage());
            return [];
        }
    }

    /* =========================================================
       4. PLACE DETAILS - Get complete place information
    ========================================================= */

    /**
     * Get place details by place_id
     */
    public function getPlaceDetails($placeId)
    {
        $cacheKey = 'place_details_' . $placeId;

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($placeId) {
            try {
                $response = Http::get($this->placesUrl . '/details/json', [
                    'place_id' => $placeId,
                    'fields' => 'name,formatted_address,geometry,address_component,plus_code,url,website,formatted_phone_number',
                    'key' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK') {
                        $result = $data['result'];
                        $location = $result['geometry']['location'];

                        return [
                            'lat' => $location['lat'],
                            'lng' => $location['lng'],
                            'formatted_address' => $result['formatted_address'],
                            'name' => $result['name'] ?? null,
                            'place_id' => $placeId,
                            'phone' => $result['formatted_phone_number'] ?? null,
                            'website' => $result['website'] ?? null,
                            'url' => $result['url'] ?? null,
                            'components' => $this->extractAddressComponents($result['address_components'] ?? [])
                        ];
                    }
                }
                return null;
            } catch (\Exception $e) {
                Log::error('Place Details Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /* =========================================================
       5. DIRECTIONS API - Route between points
    ========================================================= */

    /**
     * Get directions between origin and destination
     */
    public function getDirections($origin, $destination, $waypoints = [], $mode = 'driving')
    {
        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'mode' => $mode,
            'key' => $this->apiKey
        ];

        if (!empty($waypoints)) {
            $params['waypoints'] = 'optimize:true|' . implode('|', $waypoints);
        }

        try {
            $response = Http::get($this->directionsUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK') {
                    $route = $data['routes'][0];
                    $legs = $route['legs'];

                    $totalDistance = 0;
                    $totalDuration = 0;

                    foreach ($legs as $leg) {
                        $totalDistance += $leg['distance']['value'];
                        $totalDuration += $leg['duration']['value'];
                    }

                    return [
                        'status' => 'OK',
                        'total_distance' => $totalDistance / 1000, // km
                        'total_duration' => $totalDuration / 60, // minutes
                        'waypoint_order' => $route['waypoint_order'] ?? [],
                        'polyline' => $route['overview_polyline']['points'],
                        'legs' => $this->formatLegs($legs)
                    ];
                }
            }
            return ['status' => 'ERROR', 'message' => 'No route found'];
        } catch (\Exception $e) {
            Log::error('Directions Error: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    /**
     * Format legs for response
     */
    private function formatLegs($legs)
    {
        return collect($legs)->map(function($leg) {
            return [
                'distance' => $leg['distance']['text'],
                'distance_value' => $leg['distance']['value'],
                'duration' => $leg['duration']['text'],
                'duration_value' => $leg['duration']['value'],
                'start_address' => $leg['start_address'],
                'end_address' => $leg['end_address'],
                'start_location' => $leg['start_location'],
                'end_location' => $leg['end_location'],
                'steps' => collect($leg['steps'])->map(function($step) {
                    return [
                        'instruction' => strip_tags($step['html_instructions']),
                        'distance' => $step['distance']['text'],
                        'duration' => $step['duration']['text']
                    ];
                })
            ];
        })->toArray();
    }

    /* =========================================================
       6. DISTANCE MATRIX API - Multiple origins & destinations
    ========================================================= */

    /**
     * Get distance matrix for multiple points
     */
    public function getDistanceMatrix($origins, $destinations, $mode = 'driving')
    {
        try {
            $response = Http::get($this->distanceMatrixUrl, [
                'origins' => is_array($origins) ? implode('|', $origins) : $origins,
                'destinations' => is_array($destinations) ? implode('|', $destinations) : $destinations,
                'mode' => $mode,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK') {
                    return [
                        'status' => 'OK',
                        'origin_addresses' => $data['origin_addresses'],
                        'destination_addresses' => $data['destination_addresses'],
                        'rows' => $this->formatMatrixRows($data['rows'])
                    ];
                }
            }
            return ['status' => 'ERROR', 'message' => 'No matrix found'];
        } catch (\Exception $e) {
            Log::error('Distance Matrix Error: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    /**
     * Format matrix rows
     */
    private function formatMatrixRows($rows)
    {
        return collect($rows)->map(function($row) {
            return [
                'elements' => collect($row['elements'])->map(function($element) {
                    return [
                        'status' => $element['status'],
                        'distance' => $element['distance']['text'] ?? null,
                        'distance_value' => $element['distance']['value'] ?? null,
                        'duration' => $element['duration']['text'] ?? null,
                        'duration_value' => $element['duration']['value'] ?? null
                    ];
                })
            ];
        })->toArray();
    }

    /* =========================================================
       7. NEARBY SEARCH - Find places near location
    ========================================================= */

    /**
     * Search for nearby places
     */
    public function nearbySearch($lat, $lng, $radius = 5000, $type = null, $keyword = null)
    {
        try {
            $params = [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'key' => $this->apiKey
            ];

            if ($type) {
                $params['type'] = $type;
            }

            if ($keyword) {
                $params['keyword'] = $keyword;
            }

            $response = Http::get($this->placesUrl . '/nearbysearch/json', $params);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK') {
                    return collect($data['results'])->map(function($place) {
                        return [
                            'name' => $place['name'],
                            'address' => $place['vicinity'],
                            'lat' => $place['geometry']['location']['lat'],
                            'lng' => $place['geometry']['location']['lng'],
                            'place_id' => $place['place_id'],
                            'rating' => $place['rating'] ?? null,
                            'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                            'types' => $place['types'] ?? []
                        ];
                    })->toArray();
                }
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Nearby Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /* =========================================================
       8. FIND NEARBY DELIVERY AGENTS
    ========================================================= */

    /**
     * Find nearby delivery agents (custom implementation)
     */
    public function findNearbyAgents($lat, $lng, $radius = 5000)
    {
        // This is a custom implementation that queries your database
        // You'll need to pass this to your Agent model
        try {
            $response = Http::get($this->placesUrl . '/nearbysearch/json', [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'keyword' => 'delivery courier logistics',
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK') {
                    return collect($data['results'])->map(function($place) {
                        return [
                            'name' => $place['name'],
                            'address' => $place['vicinity'],
                            'lat' => $place['geometry']['location']['lat'],
                            'lng' => $place['geometry']['location']['lng'],
                            'place_id' => $place['place_id'],
                            'rating' => $place['rating'] ?? null
                        ];
                    })->toArray();
                }
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Nearby Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /* =========================================================
       9. CALCULATE DISTANCE BETWEEN TWO POINTS
    ========================================================= */

    /**
     * Calculate straight-line distance using Haversine formula
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /* =========================================================
       10. VALIDATE AND TEST API KEY
    ========================================================= */

    /**
     * Test if API key is valid and working
     */
    public function testApiKey()
    {
        try {
            $response = Http::get($this->geocodeUrl, [
                'address' => 'Delhi, India',
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK') {
                    return [
                        'success' => true,
                        'message' => 'API key is valid and working',
                        'status' => $data['status']
                    ];
                } elseif ($data['status'] === 'REQUEST_DENIED') {
                    return [
                        'success' => false,
                        'message' => 'API key is invalid or billing not enabled: ' . ($data['error_message'] ?? 'Unknown error'),
                        'status' => $data['status']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'API test failed',
                'status' => 'ERROR'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'API test error: ' . $e->getMessage(),
                'status' => 'EXCEPTION'
            ];
        }
    }

    /* =========================================================
       11. GET API USAGE STATS (for debugging)
    ========================================================= */

    /**
     * Get API usage statistics (if available)
     * Note: This requires additional setup with Google Cloud
     */
    public function getUsageStats()
    {
        // This would require Google Cloud API to get usage stats
        // For now, return basic info
        return [
            'api_key_masked' => substr($this->apiKey, 0, 8) . '...' . substr($this->apiKey, -4),
            'services' => [
                'geocoding' => 'Check if enabled',
                'places' => 'Check if enabled',
                'directions' => 'Check if enabled',
                'distance_matrix' => 'Check if enabled'
            ]
        ];
    }
}
