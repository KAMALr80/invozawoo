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
                return ['success' => false, 'message' => 'Neural Bridge offline. Missing credentials.'];
            }

            // Optimization: Fetch all products from WC once to map SKUs
            $existingWcProducts = [];
            $page = 1;
            
            // To be super fast, we only fetch the necessary fields (id, sku) using _fields (WC API standard)
            do {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get($this->url . 'products', [
                        'page' => $page,
                        'per_page' => 100,
                        'status' => 'any',
                        '_fields' => 'id,sku'
                    ]);
                
                $batch = $response->json();
                if (empty($batch) || !is_array($batch) || isset($batch['code'])) break;
                
                foreach ($batch as $wcItem) {
                    if (!empty($wcItem['sku'])) {
                        $existingWcProducts[$wcItem['sku']] = $wcItem['id'];
                    }
                }
                $page++;
            } while (count($batch) == 100 && $page < 20); // Support up to 2000 products for initial mapping

            $batchData = [
                'create' => [],
                'update' => []
            ];

            foreach ($products as $product) {
                $item = [
                    'name' => $product->name,
                    'type' => 'simple',
                    'regular_price' => (string)$product->price,
                    'description' => $product->description ?? '',
                    'short_description' => $product->description ? substr(strip_tags($product->description), 0, 160) : '',
                    'manage_stock' => true,
                    'stock_quantity' => (int)$product->quantity,
                    'sku' => $product->product_code,
                    'status' => 'publish',
                    'catalog_visibility' => 'visible',
                    'stock_status' => (int)$product->quantity > 0 ? 'instock' : 'outofstock'
                ];

                if ($product->image) {
                    // Fix: Ensure we use full URL for production sync
                    $imageUrl = filter_var($product->image, FILTER_VALIDATE_URL) 
                        ? $product->image 
                        : config('app.url') . '/storage/' . $product->image;
                    $item['images'] = [['src' => $imageUrl]];
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
                return ['success' => true, 'created' => 0, 'updated' => 0];
            }

            $response = Http::withBasicAuth($this->key, $this->secret)
                ->timeout(120) // High timeout for batch operation
                ->post($this->url . 'products/batch', $batchData);

            if ($response->successful()) {
                $data = $response->json();
                $createdCount = count($data['create'] ?? []);
                $updatedCount = count($data['update'] ?? []);
                
                Log::info('WooCommerce Batch Sync Success', [
                    'created' => $createdCount,
                    'updated' => $updatedCount
                ]);
                
                return [
                    'success' => true,
                    'created' => $createdCount,
                    'updated' => $updatedCount
                ];
            }

            Log::error('WooCommerce Batch Sync Failed: ' . $response->body());
            return ['success' => false, 'message' => 'Transmission Error: ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('WooCommerce Batch Sync Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Internal Pulse Failure: ' . $e->getMessage()];
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
