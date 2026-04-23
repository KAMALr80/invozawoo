<?php
// app/Http/Controllers/Logistics/ServiceAreaController.php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ServiceAreaController extends Controller
{
    /**
     * Show service areas map
     */
    public function index()
    {
        // Get all delivery agents with their current locations
        $agents = DeliveryAgent::where('is_active', true)
            ->whereNotNull('current_latitude')
            ->whereNotNull('current_longitude')
            ->get();

        // Get delivery hotspots (areas with most deliveries)
        $hotspots = Shipment::where('status', 'delivered')
            ->whereNotNull('destination_latitude')
            ->whereNotNull('destination_longitude')
            ->selectRaw('destination_latitude as lat, destination_longitude as lng, count(*) as count')
            ->groupBy('destination_latitude', 'destination_longitude')
            ->orderBy('count', 'desc')
            ->limit(50)
            ->get();

        // Get coverage areas (pincodes)
        $coverage = Shipment::select('pincode', 'city', 'state')
            ->whereNotNull('pincode')
            ->distinct()
            ->orderBy('pincode')
            ->get();

        return view('logistics.service-areas', compact('agents', 'hotspots', 'coverage'));
    }

    /**
     * Get heatmap data
     */
    public function heatmapData()
    {
        $deliveries = Shipment::where('status', 'delivered')
            ->whereNotNull('destination_latitude')
            ->whereNotNull('destination_longitude')
            ->whereMonth('created_at', now()->month)
            ->select('destination_latitude as lat', 'destination_longitude as lng')
            ->get()
            ->map(function($item) {
                return [$item->lat, $item->lng, 1];
            });

        return response()->json($deliveries);
    }
}
