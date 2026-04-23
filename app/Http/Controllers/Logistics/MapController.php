<?php
// app/Http/Controllers/Logistics/MapController.php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Show shipment tracking map
     */
    public function trackShipment($trackingNumber)
    {
        $shipment = Shipment::with(['trackings', 'customer'])
            ->where('tracking_number', $trackingNumber)
            ->orWhere('shipment_number', $trackingNumber)
            ->firstOrFail();

        // Current location (delivery boy ya last tracking point)
        if ($shipment->assigned_to) {
            $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
            $currentLat = $agent->current_latitude ?? 28.6139;
            $currentLng = $agent->current_longitude ?? 77.2090;
        } else {
            $lastTracking = $shipment->trackings()->latest()->first();
            $currentLat = $lastTracking->latitude ?? 28.6139;
            $currentLng = $lastTracking->longitude ?? 77.2090;
        }

        // Warehouse location (from .env)
        $warehouse = [
            'lat' => env('WAREHOUSE_LAT', 22.524768),
            'lng' => env('WAREHOUSE_LNG', 72.955568),
            'name' => 'Main Warehouse'
        ];

        // Destination
        $destination = [
            'lat' => $shipment->destination_latitude ?? $this->getLatFromAddress($shipment->shipping_address),
            'lng' => $shipment->destination_longitude ?? $this->getLngFromAddress($shipment->shipping_address),
            'name' => $shipment->receiver_name,
            'address' => $shipment->full_address
        ];

        // Tracking history points
        $trackingPoints = $shipment->trackings()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('tracked_at')
            ->get()
            ->map(function($track) {
                return [
                    'lat' => $track->latitude,
                    'lng' => $track->longitude,
                    'status' => $track->status,
                    'time' => $track->tracked_at->format('Y-m-d H:i:s')
                ];
            });

        return view('logistics.track', compact(
            'shipment',
            'currentLat',
            'currentLng',
            'warehouse',
            'destination',
            'trackingPoints'
        ));
    }

    private function getLatFromAddress($address)
    {
        // Default Delhi coordinates
        return 28.6139;
    }

    private function getLngFromAddress($address)
    {
        // Default Delhi coordinates
        return 77.2090;
    }
}
