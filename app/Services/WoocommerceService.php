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
    public function verifyConnection($useCache = true)
    {
        return $this->checkConnection($useCache);
    }

    /**
     * Check if API connection is valid
     */
    public function checkConnection($useCache = true)
    {
        $check = function() {
            try {
                if (empty($this->key) || empty($this->secret) || empty($this->url)) {
                    return false;
                }
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->timeout(15)
                    ->get($this->url . 'system_status');
                
                return $response->successful();
            } catch (\Exception $e) {
                Log::error('WooCommerce Connection Error: ' . $e->getMessage());
                return false;
            }
        };

        if (!$useCache) {
            return $check();
        }

        return cache()->remember('woocommerce_connection_status', 300, $check);
    }

    /**
     * Get or Create Categories in WooCommerce
     */
    protected function getWcCategoryMap()
    {
        return cache()->remember('woocommerce_category_map', 3600, function() {
            try {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get($this->url . 'products/categories', ['per_page' => 100]);
                
                if ($response->successful()) {
                    $categories = $response->json();
                    $map = [];
                    foreach ($categories as $cat) {
                        $map[strtolower($cat['name'])] = $cat['id'];
                    }
                    return $map;
                }
            } catch (\Exception $e) {
                Log::error('WooCommerce Category Fetch Error: ' . $e->getMessage());
            }
            return [];
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

            // Fetch Category Map
            $categoryMap = $this->getWcCategoryMap();

            // Optimization: Fetch all products from WC once to map SKUs
            $existingWcProducts = [];
            $page = 1;
            
            do {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get($this->url . 'products', [
                        'page' => $page,
                        'per_page' => 100,
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
            } while (count($batch) == 100 && $page < 20);

            $batchData = [
                'create' => [],
                'update' => []
            ];

            foreach ($products as $product) {
                // Map Category ID
                $categories = [];
                if ($product->category) {
                    $catName = strtolower($product->category);
                    if (isset($categoryMap[$catName])) {
                        $categories[] = ['id' => $categoryMap[$catName]];
                    }
                }

                $item = [
                    'name' => $product->name,
                    'type' => 'simple',
                    'regular_price' => (string)$product->price,
                    'description' => $product->description ?? '',
                    'short_description' => $product->description ? substr(strip_tags($product->description), 0, 160) : '',
                    'sku' => $product->product_code,
                    'manage_stock' => true,
                    'stock_quantity' => (int)$product->quantity,
                    'stock_status' => $product->quantity > 0 ? 'instock' : 'outofstock',
                    'in_stock' => $product->quantity > 0, // Legacy compatibility
                    'categories' => $categories,
                    'status' => 'publish',
                    'catalog_visibility' => 'visible',
                    'featured' => (bool)$product->is_featured
                ];

                if ($product->image) {
                    $item['images'] = [['src' => $product->image_url]];
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
                ->timeout(120)
                ->post($this->url . 'products/batch', $batchData);

            if ($response->successful()) {
                $data = $response->json();
                
                $created = collect($data['create'] ?? [])->filter(fn($item) => !isset($item['error']));
                $updated = collect($data['update'] ?? [])->filter(fn($item) => !isset($item['error']));
                $failed = collect($data['create'] ?? [])->merge($data['update'] ?? [])->filter(fn($item) => isset($item['error']));

                if ($failed->count() > 0) {
                    Log::warning('WooCommerce Sync Partial Failure', [
                        'errors' => $failed->map(fn($f) => $f['error']['message'] ?? 'Unknown error')->toArray()
                    ]);
                }

                return [
                    'success' => $created->count() > 0 || $updated->count() > 0,
                    'created' => $created->count(),
                    'updated' => $updated->count(),
                    'failed' => $failed->count(),
                    'errors' => $failed->map(fn($f) => $f['error']['message'] ?? 'Unknown error')->toArray()
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
        return $this->syncBatchProducts(collect([$product]));
    }
}
