<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\WoocommerceSetting;
use Illuminate\Support\Facades\Log;

class WoocommerceService
{
    protected $url;
    protected $key;
    protected $secret;

    public function __construct($url = null, $key = null, $secret = null)
    {
        $settings = WoocommerceSetting::getSettings();
        $this->url = $url ? rtrim($url, '/') . '/wp-json/wc/v3/' : rtrim($settings->app_url, '/') . '/wp-json/wc/v3/';
        $this->key = $key ?? $settings->consumer_key;
        $this->secret = $secret ?? $settings->consumer_secret;
    }

    /**
     * Verify connection with provided credentials (alias for checkConnection)
     */
    public function verifyConnection()
    {
        return $this->checkConnection();
    }

    /**
     * Check if API connection is valid
     */
    public function checkConnection()
    {
        return cache()->remember('woocommerce_connection_status', 300, function() {
            try {
                if (empty($this->key) || empty($this->secret) || empty($this->url)) {
                    return false;
                }
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->timeout(10)
                    ->get($this->url . 'system_status');
                
                return $response->successful();
            } catch (\Exception $e) {
                Log::error('WooCommerce Connection Error: ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Sync Batch of Products
     */
    public function syncBatchProducts($products)
    {
        try {
            $create = [];
            $update = [];

            // In a real scenario, we would check which products exist in WC.
            // For now, we'll use the existing sync logic but optimized for batching.
            // Since we don't have woocommerce_id, we still need to check SKU, 
            // but we can do it in a more optimized way if we wanted.
            
            // However, to keep it simple and effective for this request, 
            // we will process them in chunks and use the batch API.
            
            $batchData = [
                'create' => [],
                'update' => []
            ];

            foreach ($products as $product) {
                $item = [
                    'name' => $product->name,
                    'type' => 'simple',
                    'regular_price' => (string)$product->price,
                    'description' => $product->description,
                    'short_description' => substr($product->description, 0, 100),
                    'manage_stock' => true,
                    'stock_quantity' => $product->quantity,
                    'sku' => $product->product_code,
                ];

                if ($product->image) {
                    $item['images'] = [['src' => asset('storage/' . $product->image)]];
                }

                // Check if exists (still needed unless we add woocommerce_id column)
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get($this->url . 'products', ['sku' => $product->product_code]);
                
                $existing = $response->json();
                if (!empty($existing)) {
                    $item['id'] = $existing[0]['id'];
                    $batchData['update'][] = $item;
                } else {
                    $batchData['create'][] = $item;
                }
            }

            if (empty($batchData['create']) && empty($batchData['update'])) {
                return true;
            }

            return Http::withBasicAuth($this->key, $this->secret)
                ->post($this->url . 'products/batch', $batchData);

        } catch (\Exception $e) {
            Log::error('WooCommerce Batch Sync Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync Product
     */
    public function syncProduct($product)
    {
        // Existing single sync logic
        return $this->syncBatchProducts(collect([$product]));
    }
}
