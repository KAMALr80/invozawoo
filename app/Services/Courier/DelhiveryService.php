<?php
// app/Services/Courier/DelhiveryService.php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DelhiveryService
{
    protected $apiKey;
    protected $baseUrl;
    protected $productionMode;

    public function __construct()
    {
        $this->apiKey = env('DELHIVERY_API_KEY');
        $this->productionMode = env('DELHIVERY_PRODUCTION', false);
        $this->baseUrl = $this->productionMode
            ? 'https://track.delhivery.com'
            : 'https://staging-express.delhivery.com';
    }

    /**
     * Create shipment in Delhivery
     */
    public function createShipment($shipmentData)
    {
        $payload = [
            'shipments' => [
                [
                    'name' => $shipmentData['receiver_name'],
                    'phone' => $shipmentData['receiver_phone'],
                    'address' => $shipmentData['shipping_address'],
                    'city' => $shipmentData['city'],
                    'state' => $shipmentData['state'],
                    'pincode' => $shipmentData['pincode'],
                    'country' => 'India',
                    'weight' => $shipmentData['weight'] ?? 0.5,
                    'order' => $shipmentData['shipment_number'],
                    'payment_mode' => $shipmentData['payment_mode'] == 'cod' ? 'COD' : 'Pre-paid',
                    'total_amount' => $shipmentData['declared_value'],
                    'cod_amount' => $shipmentData['payment_mode'] == 'cod' ? $shipmentData['declared_value'] : 0,
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/cmu/create.json', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'waybill' => $data['waybill'] ?? null,
                    'tracking_number' => $data['tracking_number'] ?? null,
                ];
            }

            Log::error('Delhivery API Error', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'API Error'];

        } catch (\Exception $e) {
            Log::error('Delhivery Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Track shipment
     */
    public function trackShipment($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . '/api/packages', [
                'waybill' => $trackingNumber
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Delhivery Tracking Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Cancel shipment
     */
    public function cancelShipment($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->post($this->baseUrl . '/api/packages/cancel', [
                'waybill' => $trackingNumber
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Delhivery Cancel Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate label
     */
    public function generateLabel($trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . '/api/packages/label', [
                'waybill' => $trackingNumber
            ]);

            if ($response->successful()) {
                return $response->body();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Delhivery Label Error', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
