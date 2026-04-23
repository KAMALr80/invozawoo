<?php
// app/Http/Controllers/Logistics/TrackingController.php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track($trackingNumber)
    {
        $shipment = Shipment::with(['trackings'])
            ->where('tracking_number', $trackingNumber)
            ->orWhere('shipment_number', $trackingNumber)
            ->firstOrFail();

        // Get all tracking locations
        $trackingHistory = $shipment->trackings()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('tracked_at')
            ->get();

        // Current location
        $currentLocation = [
            'lat' => $shipment->current_latitude ?? 28.6139,
            'lng' => $shipment->current_longitude ?? 77.2090,
        ];

        // Origin (warehouse)
        $origin = [
            'lat' => env('WAREHOUSE_LAT', 28.6129),
            'lng' => env('WAREHOUSE_LNG', 77.2295),
            'name' => 'Warehouse'
        ];

        // Destination
        $destination = [
            'lat' => $shipment->destination_latitude ?? $this->getLatLngFromAddress($shipment->shipping_address),
            'lng' => $shipment->destination_longitude ?? $this->getLatLngFromAddress($shipment->shipping_address),
            'name' => $shipment->receiver_name,
            'address' => $shipment->full_address
        ];

        return view('logistics.tracking', compact(
            'shipment',
            'trackingHistory',
            'currentLocation',
            'origin',
            'destination'
        ));
    }

    private function getLatLngFromAddress($address)
    {
        // Use Nominatim API (FREE)
        // We'll implement this in Step 5
        return [28.6139, 77.2090]; // Default for now
    }
}
