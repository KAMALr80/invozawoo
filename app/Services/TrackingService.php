<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingService
{
    private $apiKey;
    private $apiUrl = 'https://api.trackingmore.com/v4';

    public function __construct()
    {
        $this->apiKey = config('services.trackingmore.api_key');
    }

    /**
     * Create tracking for courier
     */
    public function createTracking($courierCode, $trackingNumber, $reference = null)
    {
        try {
            $response = Http::withHeaders([
                'Tracking-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/trackings/create', [
                'tracking_number' => $trackingNumber,
                'courier_code' => $courierCode,
                'order_id' => $reference,
                'language' => 'en',
                'title' => 'Shipment Tracking'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Tracking creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get tracking info
     */
    public function getTrackingInfo($courierCode, $trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Tracking-Api-Key' => $this->apiKey
            ])->get($this->apiUrl . "/trackings/{$courierCode}/{$trackingNumber}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Tracking fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all couriers
     */
    public function getCouriers()
    {
        try {
            $response = Http::withHeaders([
                'Tracking-Api-Key' => $this->apiKey
            ])->get($this->apiUrl . '/couriers/all');

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Courier fetch failed: ' . $e->getMessage());
            return [];
        }
    }
}
