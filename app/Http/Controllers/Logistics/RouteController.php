<?php
// app/Http/Controllers/Logistics/RouteController.php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use App\Services\OSRMService;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    protected $osrm;

    public function __construct(OSRMService $osrm)
    {
        $this->osrm = $osrm;
    }

    /**
     * Show route optimization page
     */
    public function index(Request $request)
    {
        $agentId = $request->get('agent_id');
        $date = $request->get('date', now()->format('Y-m-d'));

        // Get shipments for the day
        $shipments = Shipment::with(['customer'])
            ->whereDate('estimated_delivery_date', $date)
            ->whereIn('status', ['picked', 'in_transit'])
            ->when($agentId, function($query) use ($agentId) {
                return $query->where('assigned_to', $agentId);
            })
            ->get();

        // Get all agents
        $agents = DeliveryAgent::where('is_active', true)->get();

        // Warehouse location
        $warehouse = [
            'lat' => env('WAREHOUSE_LAT', 22.524768),
            'lng' => env('WAREHOUSE_LNG', 72.955568),
            'name' => 'Warehouse'
        ];

        return view('logistics.route-planner', compact('shipments', 'agents', 'warehouse', 'date', 'agentId'));
    }

    /**
     * Calculate optimized route
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'shipment_ids' => 'required|array',
            'shipment_ids.*' => 'exists:shipments,id',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
        ]);

        $shipments = Shipment::whereIn('id', $request->shipment_ids)->get();

        // Build coordinates array
        $coordinates = [
            ['lat' => $request->start_lat, 'lng' => $request->start_lng] // Start point
        ];

        foreach ($shipments as $shipment) {
            $coordinates[] = [
                'lat' => $shipment->destination_latitude ?? $this->getLatFromAddress($shipment->shipping_address),
                'lng' => $shipment->destination_longitude ?? $this->getLngFromAddress($shipment->shipping_address),
                'id' => $shipment->id,
                'address' => $shipment->full_address,
                'customer' => $shipment->receiver_name
            ];
        }

        // Get optimized route from OSRM
        $route = $this->osrm->getOptimizedRoute($coordinates);

        if (!$route) {
            return response()->json(['error' => 'Could not calculate route'], 500);
        }

        return response()->json([
            'success' => true,
            'route' => $route,
            'coordinates' => $coordinates,
            'total_distance' => $route['distance'],
            'total_duration' => $route['duration'],
            'waypoints' => $coordinates
        ]);
    }

    /**
     * Assign optimized route to agent
     */
    public function assign(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:delivery_agents,user_id',
            'shipment_ids' => 'required|array',
            'route_order' => 'required|array',
        ]);

        // Assign shipments to agent in order
        foreach ($request->route_order as $index => $shipmentId) {
            $shipment = Shipment::find($shipmentId);
            $shipment->assigned_to = $request->agent_id;
            $shipment->delivery_order = $index + 1;
            $shipment->save();

            // Add tracking
            $shipment->updateTracking(
                'assigned',
                null,
                "Assigned to agent with delivery order #" . ($index + 1)
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Route assigned successfully'
        ]);
    }

    private function getLatFromAddress($address)
    {
        // You can use GeocodingService here
        return 22.524768; // Default
    }

    private function getLngFromAddress($address)
    {
        return 72.955568; // Default
    }
}
