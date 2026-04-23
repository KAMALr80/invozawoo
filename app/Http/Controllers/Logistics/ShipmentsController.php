<?php
// app/Http/Controllers/Logistics/ShipmentsController.php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentsController extends Controller
{
    public function index(Request $request)
    {
        // ✅ DEBUG: Log all shipments including sale_id
        Log::info('Shipments Query - Checking all shipments', [
            'total_shipments' => Shipment::count(),
            'shipments_with_sale_id' => Shipment::whereNotNull('sale_id')->count(),
            'shipments_without_sale_id' => Shipment::whereNull('sale_id')->count()
        ]);

        $query = Shipment::with(['customer', 'deliveryAgent', 'sale']);

        if ($request->source === 'sales') {
            $query->whereNotNull('sale_id');
            Log::info('Filtering by source: sales', ['count' => $query->count()]);
        } elseif ($request->source === 'direct') {
            $query->whereNull('sale_id');
        }

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tracking_number', 'LIKE', "%{$request->search}%")
                    ->orWhere('shipment_number', 'LIKE', "%{$request->search}%")
                    ->orWhere('receiver_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('city', 'LIKE', "%{$request->search}%")
                    ->orWhere('receiver_phone', 'LIKE', "%{$request->search}%")
                    ->orWhereHas('sale', function ($sq) use ($request) {
                        $sq->where('invoice_no', 'LIKE', "%{$request->search}%");
                    });
            });
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $shipments = $query->latest()->paginate(20)->withQueryString();

        // ✅ Get all shipments (for debugging)
        $allShipments = Shipment::with('sale')->latest()->limit(5)->get();
        Log::info('Recent 5 shipments', $allShipments->map(function($s) {
            return [
                'id' => $s->id,
                'shipment_number' => $s->shipment_number,
                'sale_id' => $s->sale_id,
                'receiver_name' => $s->receiver_name,
                'city' => $s->city
            ];
        })->toArray());

        $stats = [
            'total' => Shipment::count(),
            'pending' => Shipment::where('status', 'pending')->count(),
            'in_transit' => Shipment::whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->count(),
            'delivered_today' => Shipment::whereDate('actual_delivery_date', today())->count(),
            'from_sales' => Shipment::whereNotNull('sale_id')->count(),
            'direct' => Shipment::whereNull('sale_id')->count(),
        ];

        return view('logistics.shipments.index', compact('shipments', 'stats', 'request'));
    }

    public function track($trackingNumber)
    {
        try {
            $shipment = Shipment::with(['trackings', 'customer', 'sale'])
                ->where('tracking_number', $trackingNumber)
                ->orWhere('shipment_number', $trackingNumber)
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found'
                ], 404);
            }

            $latestTracking = $shipment->trackings()->latest('tracked_at')->first();

            return response()->json([
                'success' => true,
                'shipment_number' => $shipment->shipment_number,
                'tracking_number' => $shipment->tracking_number,
                'receiver_name' => $shipment->receiver_name,
                'status' => $shipment->status,
                'latitude' => $shipment->destination_latitude ?? $latestTracking?->latitude,
                'longitude' => $shipment->destination_longitude ?? $latestTracking?->longitude,
                'estimated_delivery' => $shipment->estimated_delivery_date?->format('d M Y'),
                'current_location' => $latestTracking?->location ?? $shipment->city,
                'last_location_update' => $latestTracking?->tracked_at?->format('d M Y, h:i A'),
                'accuracy' => $latestTracking?->accuracy,
                'tracking_history' => $shipment->trackings->map(function($track) {
                    return [
                        'status' => $track->status,
                        'location' => $track->location,
                        'remarks' => $track->remarks,
                        'tracked_at' => $track->tracked_at->format('d M Y, h:i A')
                    ];
                }),
                'invoice_no' => $shipment->sale?->invoice_no,
                'from_sale' => !is_null($shipment->sale_id)
            ]);
        } catch (\Exception $e) {
            Log::error('Tracking Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tracking data'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,picked,in_transit,out_for_delivery,delivered,failed,returned',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric'
        ]);

        try {
            $shipment = Shipment::findOrFail($id);

            $tracking = $shipment->trackings()->create([
                'status' => $request->status,
                'location' => $request->location ?? $shipment->city,
                'remarks' => $request->remarks,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'tracked_at' => now()
            ]);

            $shipment->status = $request->status;

            if ($request->status === 'delivered') {
                $shipment->actual_delivery_date = now();

                if ($shipment->sale_id) {
                    $sale = Sale::find($shipment->sale_id);
                    if ($sale) {
                        $sale->shipping_status = 'delivered';
                        $sale->save();
                        Log::info("📦 Sale #{$sale->invoice_no} shipping status updated to delivered");
                    }
                }
            }

            if ($request->latitude && $request->longitude) {
                $shipment->current_latitude = $request->latitude;
                $shipment->current_longitude = $request->longitude;
            }

            $shipment->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'tracking' => $tracking
            ]);
        } catch (\Exception $e) {
            Log::error('Status Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric'
        ]);

        try {
            $shipment = Shipment::findOrFail($id);

            $shipment->trackings()->create([
                'status' => $shipment->status,
                'location' => $shipment->city,
                'remarks' => 'Live location update',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'tracked_at' => now()
            ]);

            $shipment->current_latitude = $request->latitude;
            $shipment->current_longitude = $request->longitude;
            $shipment->last_location_update = now();
            $shipment->save();

            return response()->json([
                'success' => true,
                'message' => 'Location updated'
            ]);
        } catch (\Exception $e) {
            Log::error('Location Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating location'
            ], 500);
        }
    }

    public function show($id)
    {
        $shipment = Shipment::with(['customer', 'deliveryAgent', 'trackings', 'sale.customer', 'sale.items.product'])
            ->findOrFail($id);

        return view('logistics.shipments.show', compact('shipment'));
    }
}
