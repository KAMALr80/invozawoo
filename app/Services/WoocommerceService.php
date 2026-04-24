<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\WoocommerceSetting;
use App\Models\WoocommerceSyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WoocommerceService
{
    protected $url;
    protected $key;
    protected $secret;
    protected $business_id;

    public function __construct()
    {
        $settings = WoocommerceSetting::first();
        if ($settings) {
            $this->url = rtrim($settings->app_url, '/');
            $this->key = $settings->consumer_key;
            $this->secret = $settings->consumer_secret;
        }
    }

    public function checkConnection($force = false)
    {
        if (!$this->url || !$this->key || !$this->secret) {
            return ['success' => false, 'message' => 'Credentials not configured'];
        }

        $cacheKey = 'wc_connection_status';
        if (!$force && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withBasicAuth($this->key, $this->secret)
                ->timeout(30)
                ->get("{$this->url}/wp-json/wc/v3/system_status");

            $result = $response->successful() 
                ? ['success' => true, 'message' => 'Connected successfully']
                : ['success' => false, 'message' => 'Failed to connect: ' . ($response->json()['message'] ?? 'Unknown error')];
            
            Cache::put($cacheKey, $result, now()->addMinutes(10));
            return $result;
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }

    /**
     * Get WooCommerce Categories and cache them
     */
    public function getCategories()
    {
        return Cache::remember('wc_categories', now()->addHours(1), function () {
            try {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get("{$this->url}/wp-json/wc/v3/products/categories", ['per_page' => 100]);
                
                if ($response->successful()) {
                    return collect($response->json())->pluck('id', 'name')->toArray();
                }
            } catch (\Exception $e) {
                Log::error("WC Category Fetch Error: " . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Synchronize Products (Smart Sync)
     */
    public function syncBatchProducts($productIds = null, $syncType = 'all')
    {
        $query = Product::where('is_active', true);

        if ($productIds) {
            $query->whereIn('id', $productIds);
        }

        // Smart Sync Logic
        if ($syncType === 'smart') {
            $query->where(function($q) {
                $q->whereNull('synced_at')
                  ->orWhereRaw('updated_at > synced_at');
            });
        }

        $products = $query->get();
        if ($products->isEmpty()) {
            return [
                'success' => true, 
                'success_count' => 0, 
                'failed_count' => 0, 
                'message' => 'No products to sync'
            ];
        }

        $wcCategories = $this->getCategories();
        
        // Auto-map existing products by SKU if they don't have ID locally
        $this->mapExistingProducts($products);

        $batchData = ['create' => [], 'update' => []];
        $idMap = [];

        foreach ($products as $product) {
            // Skip products without code/sku
            if (empty($product->product_code)) {
                continue;
            }

            // Find or Create category ID
            $categories = [];
            if ($product->category) {
                $catId = $this->getOrCreateCategory($product->category, $wcCategories);
                if ($catId) {
                    $categories[] = ['id' => $catId];
                }
            }

            // Brand Mapping (common for brand plugins)
            $brands = [];
            if ($product->brand) {
                $brandId = $this->getOrCreateBrand($product->brand);
                if ($brandId) {
                    $brands[] = ['id' => $brandId];
                }
            }

            $item = [
                'name' => (string)$product->name,
                'type' => 'simple',
                'regular_price' => (string)$product->price,
                'description' => (string)($product->description ?? ''),
                'short_description' => (string)($product->description ?? ''),
                'sku' => (string)$product->product_code,
                'manage_stock' => true,
                'stock_quantity' => (int)$product->quantity,
                'stock_status' => $product->quantity > 0 ? 'instock' : 'outofstock',
                'categories' => $categories,
                'status' => 'publish',
                'catalog_visibility' => 'visible',
            ];

            $item['images'] = [['src' => $product->image_url]];

            if ($product->woocommerce_product_id) {
                $item['id'] = $product->woocommerce_product_id;
                $batchData['update'][] = $item;
            } else {
                $batchData['create'][] = $item;
            }
            $idMap[$product->product_code] = $product;
        }

        try {
            $response = Http::withBasicAuth($this->key, $this->secret)
                ->timeout(120)
                ->post("{$this->url}/wp-json/wc/v3/products/batch", $batchData);

            if ($response->successful()) {
                $data = $response->json();
                $successCount = 0;
                $failedItems = [];

                // Process Created
                foreach ($data['create'] ?? [] as $item) {
                    if (isset($item['id']) && isset($item['sku'])) {
                        $product = $idMap[$item['sku']] ?? null;
                        if ($product) {
                            $product->update([
                                'woocommerce_product_id' => $item['id'],
                                'synced_at' => now()
                            ]);
                            $successCount++;
                        }
                    } else if (isset($item['error'])) {
                        $failedItems[] = "Create failed for {$item['sku']}: " . ($item['error']['message'] ?? 'Unknown');
                    }
                }

                // Process Updated
                foreach ($data['update'] ?? [] as $item) {
                    if (isset($item['id']) && isset($item['sku'])) {
                        $product = $idMap[$item['sku']] ?? null;
                        if ($product) {
                            $product->update(['synced_at' => now()]);
                            $successCount++;
                        }
                    } else if (isset($item['error'])) {
                        $failedItems[] = "Update failed for {$item['sku']}: " . ($item['error']['message'] ?? 'Unknown');
                    }
                }

                $this->createLog('product_sync', count($products), $successCount, count($failedItems), $failedItems);

                return [
                    'success' => true,
                    'total' => count($products),
                    'success_count' => $successCount,
                    'failed_count' => count($failedItems),
                    'errors' => $failedItems
                ];
            }
            
            return [
                'success' => false, 
                'message' => 'WooCommerce API Error: ' . ($response->json()['message'] ?? 'Status Code ' . $response->status())
            ];
        } catch (\Exception $e) {
            Log::error("WooCommerce Sync Error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Sync error: ' . $e->getMessage()];
        }
    }

    /**
     * Synchronize Orders from WooCommerce
     */
    public function syncOrders()
    {
        try {
            $response = Http::withBasicAuth($this->key, $this->secret)
                ->get("{$this->url}/wp-json/wc/v3/orders", ['status' => 'any', 'per_page' => 50]);

            if ($response->successful()) {
                $orders = $response->json();
                $syncedCount = 0;
                $errors = [];

                foreach ($orders as $wcOrder) {
                    // Check if already synced
                    if (Sale::where('woocommerce_order_id', $wcOrder['id'])->exists()) {
                        continue;
                    }

                    DB::beginTransaction();
                    try {
                        // 1. Get or Create Customer
                        $customer = $this->getOrCreateCustomer($wcOrder['billing'], $wcOrder['customer_id']);

                        // 2. Create Sale
                        $sale = Sale::create([
                            'woocommerce_order_id' => $wcOrder['id'],
                            'customer_id' => $customer->id,
                            'invoice_no' => 'WC-' . $wcOrder['number'],
                            'invoice_token' => bin2hex(random_bytes(16)),
                            'sale_date' => date('Y-m-d H:i:s', strtotime($wcOrder['date_created'])),
                            'sub_total' => $wcOrder['total'] - $wcOrder['total_tax'],
                            'tax_amount' => $wcOrder['total_tax'],
                            'grand_total' => $wcOrder['total'],
                            'payment_status' => $wcOrder['status'] === 'completed' ? 'paid' : 'pending',
                            'paid_amount' => $wcOrder['status'] === 'completed' ? $wcOrder['total'] : 0,
                            'shipping_address' => $wcOrder['shipping']['address_1'] . ' ' . ($wcOrder['shipping']['address_2'] ?? ''),
                            'city' => $wcOrder['shipping']['city'],
                            'state' => $wcOrder['shipping']['state'],
                            'pincode' => $wcOrder['shipping']['postcode'],
                            'receiver_name' => $wcOrder['shipping']['first_name'] . ' ' . $wcOrder['shipping']['last_name'],
                            'receiver_phone' => $wcOrder['billing']['phone'] ?? '',
                            'shipping_status' => $this->mapOrderStatus($wcOrder['status']),
                            'created_by' => 1 // Default System Admin
                        ]);

                        // 3. Create Sale Items & Update Stock
                        foreach ($wcOrder['line_items'] as $item) {
                            $product = Product::where('product_code', $item['sku'])->first();
                            if ($product) {
                                SaleItem::create([
                                    'sale_id' => $sale->id,
                                    'product_id' => $product->id,
                                    'quantity' => $item['quantity'],
                                    'price' => $item['price'],
                                    'total' => $item['total']
                                ]);

                                // Deduct Stock
                                $product->decrement('quantity', $item['quantity']);
                            }
                        }

                        DB::commit();
                        $syncedCount++;
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $errors[] = "Order #{$wcOrder['id']} failed: " . $e->getMessage();
                    }
                }

                $this->createLog('order_sync', count($orders), $syncedCount, count($errors), $errors);
                return ['success' => true, 'synced' => $syncedCount, 'errors' => $errors];
            }

            return [
                'success' => false, 
                'message' => 'WooCommerce API Error: ' . ($response->json()['message'] ?? 'Status Code ' . $response->status())
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Order Sync error: ' . $e->getMessage()];
        }
    }

    protected function mapExistingProducts($products)
    {
        $productsWithoutId = $products->filter(fn($p) => empty($p->woocommerce_product_id));
        if ($productsWithoutId->isEmpty()) return;

        foreach ($productsWithoutId as $product) {
            try {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->get("{$this->url}/wp-json/wc/v3/products", ['sku' => $product->product_code]);

                if ($response->successful() && !empty($response->json())) {
                    $wcProduct = $response->json()[0];
                    $product->update([
                        'woocommerce_product_id' => $wcProduct['id'],
                        'synced_at' => now()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("SKU Mapping Error for {$product->product_code}: " . $e->getMessage());
            }
        }
    }

    protected function getOrCreateCategory($name, &$wcCategories)
    {
        if (isset($wcCategories[$name])) {
            return $wcCategories[$name];
        }

        try {
            $response = Http::withBasicAuth($this->key, $this->secret)
                ->post("{$this->url}/wp-json/wc/v3/products/categories", ['name' => $name]);

            if ($response->successful()) {
                $data = $response->json();
                $wcCategories[$name] = $data['id']; // Cache for current batch
                return $data['id'];
            }
        } catch (\Exception $e) {
            Log::error("WC Category Create Error: " . $e->getMessage());
        }

        return null;
    }

    protected function getOrCreateBrand($name)
    {
        // Many WooCommerce brand plugins use a custom taxonomy 'product_brand'
        // We'll attempt to find/create it via terms API if possible, or just skip
        // This is a common pattern for brands
        return null; // Placeholder for now as we don't know the specific plugin
    }

    protected function getOrCreateCustomer($billing, $wcCustomerId)
    {
        $customer = Customer::where('woocommerce_customer_id', $wcCustomerId)->first();
        if (!$customer && !empty($billing['email'])) {
            $customer = Customer::where('email', $billing['email'])->first();
        }

        if (!$customer) {
            $customer = Customer::create([
                'woocommerce_customer_id' => $wcCustomerId ?: null,
                'name' => $billing['first_name'] . ' ' . $billing['last_name'],
                'mobile' => $billing['phone'] ?? '',
                'email' => $billing['email'] ?? '',
                'address' => $billing['address_1'],
                'city' => $billing['city'],
                'state' => $billing['state'],
                'pincode' => $billing['postcode'],
                'country' => $billing['country']
            ]);
        } else if (!$customer->woocommerce_customer_id && $wcCustomerId) {
            $customer->update(['woocommerce_customer_id' => $wcCustomerId]);
        }

        return $customer;
    }

    protected function mapOrderStatus($wcStatus)
    {
        $map = [
            'pending' => 'pending',
            'processing' => 'shipped',
            'completed' => 'delivered',
            'cancelled' => 'cancelled',
            'refunded' => 'cancelled',
            'failed' => 'pending'
        ];
        return $map[$wcStatus] ?? 'pending';
    }

    protected function createLog($type, $total, $success, $failed, $details)
    {
        WoocommerceSyncLog::create([
            'operation_type' => $type,
            'items_total' => $total,
            'items_success' => $success,
            'items_failed' => $failed,
            'status' => $failed > 0 ? ($success > 0 ? 'partial' : 'failed') : 'success',
            'details' => $details
        ]);
    }
}
