<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateShipmentController extends Controller
{
    /**
     * Show edit form
     */
    public function edit($id)
    {
        $shipment = Shipment::with(['customer', 'sale'])->findOrFail($id);
        return view('logistics.shipments.edit', compact('shipment'));
    }

    /**
     * Update shipment
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request
        Log::info('=== SHIPMENT UPDATE STARTED ===', [
            'shipment_id' => $id,
            'request_data' => $request->except(['_token', '_method'])
        ]);

        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_alternate_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'package_type' => 'nullable|string|in:box,envelope,pallet',
            'courier_partner' => 'nullable|string',
            'tracking_number' => 'nullable|string|max:255',
            'awb_number' => 'nullable|string|max:255',
            'shipping_method' => 'nullable|in:standard,express,overnight',
            'pickup_date' => 'nullable|date',
            'estimated_delivery_date' => 'nullable|date|after:today',
            'status' => 'nullable|in:pending,picked,in_transit,out_for_delivery,delivered,failed,returned',
            'status_note' => 'nullable|string',
            'shipping_charge' => 'nullable|numeric|min:0',
            'cod_charge' => 'nullable|numeric|min:0',
            'insurance_charge' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|in:prepaid,cod',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $shipment = Shipment::findOrFail($id);
            $oldStatus = $shipment->status;

            Log::info('Found shipment:', [
                'id' => $shipment->id,
                'number' => $shipment->shipment_number,
                'old_status' => $oldStatus
            ]);

            // Update receiver information
            $shipment->receiver_name = $request->receiver_name;
            $shipment->receiver_phone = $request->receiver_phone;
            $shipment->receiver_alternate_phone = $request->receiver_alternate_phone;

            // Update address information
            $shipment->shipping_address = $request->shipping_address;
            $shipment->landmark = $request->landmark;
            $shipment->city = $request->city;
            $shipment->state = $request->state;
            $shipment->pincode = $request->pincode;
            $shipment->country = $request->country ?? 'India';

            // Update package details
            $shipment->weight = $request->weight;
            $shipment->length = $request->length;
            $shipment->width = $request->width;
            $shipment->height = $request->height;
            $shipment->quantity = $request->quantity ?? 1;
            $shipment->package_type = $request->package_type;

            // Update courier information
            $shipment->courier_partner = $request->courier_partner;
            $shipment->tracking_number = $request->tracking_number;
            $shipment->awb_number = $request->awb_number;
            $shipment->shipping_method = $request->shipping_method;

            // Update dates
            $shipment->pickup_date = $request->pickup_date ? Carbon::parse($request->pickup_date) : null;
            $shipment->estimated_delivery_date = $request->estimated_delivery_date ? Carbon::parse($request->estimated_delivery_date) : null;

            // Update charges
            $shipment->shipping_charge = $request->shipping_charge ?? 0;
            $shipment->cod_charge = $request->cod_charge ?? 0;
            $shipment->insurance_charge = $request->insurance_charge ?? 0;

            // Calculate total charge
            $shipment->total_charge = ($request->shipping_charge ?? 0) +
                                      ($request->cod_charge ?? 0) +
                                      ($request->insurance_charge ?? 0);

            $shipment->payment_mode = $request->payment_mode;

            // Update status
            $shipment->status = $request->status ?? $shipment->status;
            $shipment->status_note = $request->status_note;

            // Save the shipment
            $shipment->save();

            Log::info('Shipment saved successfully', [
                'id' => $shipment->id,
                'new_status' => $shipment->status
            ]);

            // If status changed, add tracking history
            if ($oldStatus != $shipment->status) {
                $shipment->updateTracking(
                    $shipment->status,
                    $request->location ?? $shipment->city,
                    $request->status_note ?? "Status updated to {$shipment->status}"
                );
                Log::info('Tracking history added for status change', [
                    'old_status' => $oldStatus,
                    'new_status' => $shipment->status
                ]);
            }

            DB::commit();

            Log::info('Shipment updated successfully', [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'updated_by' => auth()->id()
            ]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shipment updated successfully!',
                    'redirect' => route('logistics.shipments.show', $shipment->id),
                    'shipment' => [
                        'id' => $shipment->id,
                        'number' => $shipment->shipment_number,
                        'status' => $shipment->status
                    ]
                ]);
            }

            return redirect()->route('logistics.shipments.show', $shipment->id)
                ->with('success', 'Shipment updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Shipment update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'shipment_id' => $id,
                'request' => $request->all()
            ]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating shipment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error updating shipment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Quick update shipment status (AJAX endpoint)
     */
    public function updateStatus(Request $request, $id)
    {
        Log::info('=== QUICK STATUS UPDATE STARTED ===', [
            'shipment_id' => $id,
            'request_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,picked,in_transit,out_for_delivery,delivered,failed,returned',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $shipment = Shipment::findOrFail($id);
            $oldStatus = $shipment->status;

            // Update status
            $shipment->status = $request->status;
            $shipment->save();

            // Add tracking
            $shipment->updateTracking(
                $request->status,
                $request->location ?? $shipment->city,
                $request->remarks ?? "Status updated to {$request->status}"
            );

            DB::commit();

            Log::info('Shipment status updated via AJAX', [
                'shipment_id' => $shipment->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'shipment' => [
                    'id' => $shipment->id,
                    'number' => $shipment->shipment_number,
                    'status' => $shipment->status,
                    'status_badge' => $shipment->status_badge
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Quick status update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}
