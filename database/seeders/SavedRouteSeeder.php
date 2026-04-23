<?php

namespace Database\Seeders;

use App\Models\SavedRoute;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavedRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SavedRoute::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $agents = DeliveryAgent::all();

        if ($agents->isEmpty()) {
            $this->command->warn('⚠️ No delivery agents found. Skipping SavedRouteSeeder.');
            return;
        }

        $routes = [
            [
                'name' => 'Bangalore North Route',
                'agent' => 'Bangalore',
                'waypoints' => [
                    ['lat' => 12.9716, 'lng' => 77.5946, 'address' => 'Hebbal'],
                    ['lat' => 13.0358, 'lng' => 77.5970, 'address' => 'Yelahanka'],
                    ['lat' => 13.0522, 'lng' => 77.6637, 'address' => 'Kannur'],
                    ['lat' => 13.0204, 'lng' => 77.7122, 'address' => 'Bidarahalli'],
                ],
                'shipments' => [1, 2, 3, 4],
                'distance' => 28.5,
                'duration' => 75,
                'start' => ['lat' => 12.9716, 'lng' => 77.5946, 'address' => 'Warehouse, Hebbal'],
                'end' => ['lat' => 13.0204, 'lng' => 77.7122, 'address' => 'Bidarahalli'],
                'status' => 'completed',
            ],
            [
                'name' => 'Mumbai Western Route',
                'agent' => 'Mumbai',
                'waypoints' => [
                    ['lat' => 19.0760, 'lng' => 72.8777, 'address' => 'Bandra'],
                    ['lat' => 19.1136, 'lng' => 72.8697, 'address' => 'Juhu'],
                    ['lat' => 19.1364, 'lng' => 72.8290, 'address' => 'Versova'],
                    ['lat' => 19.1809, 'lng' => 72.8553, 'address' => 'Malad'],
                ],
                'shipments' => [5, 6, 7, 8],
                'distance' => 22.3,
                'duration' => 60,
                'start' => ['lat' => 19.0760, 'lng' => 72.8777, 'address' => 'Warehouse, Bandra'],
                'end' => ['lat' => 19.1809, 'lng' => 72.8553, 'address' => 'Malad'],
                'status' => 'assigned',
            ],
            [
                'name' => 'Delhi South Route',
                'agent' => 'Delhi',
                'waypoints' => [
                    ['lat' => 28.6139, 'lng' => 77.2090, 'address' => 'Connaught Place'],
                    ['lat' => 28.5672, 'lng' => 77.2126, 'address' => 'Hauz Khas'],
                    ['lat' => 28.5440, 'lng' => 77.1904, 'address' => 'Saket'],
                    ['lat' => 28.5022, 'lng' => 77.2080, 'address' => 'Mehrauli'],
                ],
                'shipments' => [9, 10, 11, 12],
                'distance' => 18.7,
                'duration' => 50,
                'start' => ['lat' => 28.6139, 'lng' => 77.2090, 'address' => 'Warehouse, CP'],
                'end' => ['lat' => 28.5022, 'lng' => 77.2080, 'address' => 'Mehrauli'],
                'status' => 'draft',
            ],
            [
                'name' => 'Pune East Route',
                'agent' => 'Pune',
                'waypoints' => [
                    ['lat' => 18.5204, 'lng' => 73.8567, 'address' => 'Shivaji Nagar'],
                    ['lat' => 18.5308, 'lng' => 73.8737, 'address' => 'Koregaon Park'],
                    ['lat' => 18.5547, 'lng' => 73.9133, 'address' => 'Kalyani Nagar'],
                    ['lat' => 18.5679, 'lng' => 73.9100, 'address' => 'Viman Nagar'],
                ],
                'shipments' => [13, 14, 15, 16],
                'distance' => 15.2,
                'duration' => 40,
                'start' => ['lat' => 18.5204, 'lng' => 73.8567, 'address' => 'Warehouse, Shivaji Nagar'],
                'end' => ['lat' => 18.5679, 'lng' => 73.9100, 'address' => 'Viman Nagar'],
                'status' => 'completed',
            ],
            [
                'name' => 'Chennai Central Route',
                'agent' => 'Chennai',
                'waypoints' => [
                    ['lat' => 13.0827, 'lng' => 80.2707, 'address' => 'Central'],
                    ['lat' => 13.0569, 'lng' => 80.2425, 'address' => 'T Nagar'],
                    ['lat' => 12.9941, 'lng' => 80.2437, 'address' => 'Adyar'],
                    ['lat' => 12.9141, 'lng' => 80.2276, 'address' => 'Thiruvanmiyur'],
                ],
                'shipments' => [17, 18, 19, 20],
                'distance' => 20.8,
                'duration' => 55,
                'start' => ['lat' => 13.0827, 'lng' => 80.2707, 'address' => 'Warehouse, Central'],
                'end' => ['lat' => 12.9141, 'lng' => 80.2276, 'address' => 'Thiruvanmiyur'],
                'status' => 'assigned',
            ],
        ];

        foreach ($routes as $index => $routeData) {
            // Find agent by city
            $agent = $agents->where('city', $routeData['agent'])->first();

            if (!$agent) {
                $agent = $agents->random();
            }

            $route = SavedRoute::create([
                'route_code' => SavedRoute::generateRouteCode(),
                'name' => $routeData['name'],
                'agent_id' => $agent->id,
                'route_date' => now()->subDays(rand(0, 10))->format('Y-m-d'),
                'waypoints' => $routeData['waypoints'],
                'shipment_ids' => $routeData['shipments'],
                'total_distance' => $routeData['distance'],
                'total_duration' => $routeData['duration'],
                'optimized_order' => $routeData['shipments'],
                'polyline' => $this->generateMockPolyline($routeData['waypoints']),
                'start_lat' => $routeData['start']['lat'],
                'start_lng' => $routeData['start']['lng'],
                'start_address' => $routeData['start']['address'],
                'end_lat' => $routeData['end']['lat'],
                'end_lng' => $routeData['end']['lng'],
                'end_address' => $routeData['end']['address'],
                'status' => $routeData['status'],
                'created_by' => 1,
            ]);

            $this->command->info("✅ Route created: {$route->name} ({$route->route_code})");
        }

        $this->command->info('✅ ' . count($routes) . ' saved routes seeded successfully!');
    }

    /**
     * Generate mock polyline (simplified)
     */
    private function generateMockPolyline($waypoints)
    {
        // In real scenario, this would be actual polyline from Google Maps
        return 'mock_polyline_' . md5(json_encode($waypoints));
    }
}
