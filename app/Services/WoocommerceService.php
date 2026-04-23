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
            if (empty($this->key) || empty($this->secret) || empty($this->url)) {
                return false;
            }

            // Optimization: Fetch all products from WC once to map SKUs
            // In a production app, we would cache this or use a local mapping table
            $existingWcProducts = [];
            $page = 1;
            
            // To be super fast, we only fetch the necessary fields (id, sku)
            do {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get($this->url . 'products', [
                        'page' => $page,
                        'per_page' => 100,
                        'fields' => 'id,sku'
                    ]);
                
                $batch = $response->json();
                if (empty($batch) || !is_array($batch)) break;
                
                foreach ($batch as $wcItem) {
                    if (!empty($wcItem['sku'])) {
                        $existingWcProducts[$wcItem['sku']] = $wcItem['id'];
                    }
                }
                $page++;
            } while (count($batch) == 100 && $page < 5); // Limit to 500 for safety in this pass

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
                    'short_description' => substr(strip_tags($product->description), 0, 160),
                    'manage_stock' => true,
                    'stock_quantity' => (int)$product->quantity,
                    'sku' => $product->product_code,
                    'status' => 'publish'
                ];

                if ($product->image) {
                    $item['images'] = [['src' => asset('storage/' . $product->image)]];
                }

                // Check if SKU exists in our fetched map
                if (isset($existingWcProducts[$product->product_code])) {
                    $item['id'] = $existingWcProducts[$product->product_code];
                    $batchData['update'][] = $item;
                } else {
                    $batchData['create'][] = $item;
                }
            }

            if (empty($batchData['create']) && empty($batchData['update'])) {
                return true;
            }

            $response = Http::withBasicAuth($this->key, $this->secret)
                ->timeout(60) // High timeout for batch operation
                ->post($this->url . 'products/batch', $batchData);

            if ($response->successful()) {
                Log::info('WooCommerce Batch Sync Success', [
                    'created' => count($batchData['create']),
                    'updated' => count($batchData['update'])
                ]);
                return true;
            }

            Log::error('WooCommerce Batch Sync Failed: ' . $response->body());
            return false;

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
