<?php

namespace App\Utils;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Automattic\WooCommerce\Client;
use Exception;

class WoocommerceUtil
{
    /**
     * Get WooCommerce API Client
     */
    public function getClient()
    {
        $settings = DB::table('woocommerce_settings')->first();

        if (!$settings) {
            throw new Exception("WooCommerce settings not configured.");
        }

        return new Client(
            $settings->store_url,
            $settings->consumer_key,
            $settings->consumer_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 30,
                'verify_ssl' => false
            ]
        );
    }

    /**
     * Sync Customers from ERP to WooCommerce
     */
    public function syncCustomers()
    {
        $woocommerce = $this->getClient();
        $customers = Customer::whereNull('woocommerce_customer_id')->get();
        $syncedIds = [];

        foreach ($customers as $customer) {
            try {
                $nameParts = explode(' ', $customer->name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

                $data = [
                    'email' => $customer->email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $customer->mobile ?: $customer->email,
                    'billing' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'address_1' => $customer->address,
                        'city' => $customer->city,
                        'state' => $customer->state,
                        'postcode' => $customer->pincode,
                        'country' => 'IN',
                        'email' => $customer->email,
                        'phone' => $customer->mobile
                    ]
                ];

                $response = $woocommerce->post('customers', $data);
                $customer->woocommerce_customer_id = $response->id;
                $customer->save();
                $syncedIds[] = $response->id;
            } catch (\Exception $e) {
                Log::channel('woocommerce')->error("WC Customer Sync Error for {$customer->name}: " . $e->getMessage());
            }
        }

        return $syncedIds;
    }

    /**
     * Sync Products from ERP to WooCommerce
     */
    public function syncProducts($productId = null)
    {
        $woocommerce = $this->getClient();
        $query = Product::where('woocommerce_sync_disabled', 0);
        
        if ($productId) {
            $query->where('id', $productId);
        }
        
        $products = $query->get();
        $results = [
            'synced_ids' => [],
            'errors' => []
        ];

        foreach ($products as $product) {
            try {
                // 1. Handle Categories (Find or Create)
                $categoryIds = [];
                if ($product->category) {
                    $categoryIds = $this->getOrCreateCategory($woocommerce, $product->category);
                }

                // 2. Prepare Data
                $data = [
                    'name' => $product->name,
                    'type' => 'simple',
                    'status' => 'publish', // Ensure it shows up in store
                    'regular_price' => (string)$product->price,
                    'description' => $product->description ?: $product->name,
                    'short_description' => substr($product->description ?: $product->name, 0, 150),
                    'sku' => $product->product_code,
                    'manage_stock' => true,
                    'stock_quantity' => (int)$product->quantity,
                    'stock_status' => $product->quantity > 0 ? 'instock' : 'outofstock',
                    'categories' => array_map(function($id) { return ['id' => $id]; }, $categoryIds),
                ];

                // 3. Handle Images (Only if public URL)
                if ($product->image) {
                    $imageUrl = asset('storage/' . $product->image);
                    // Check if it's not a localhost URL (WooCommerce needs public URL)
                    if (!str_contains($imageUrl, 'localhost') && !str_contains($imageUrl, '127.0.0.1')) {
                        $data['images'] = [['src' => $imageUrl]];
                    }
                }

                // 4. Handle Existing Product by SKU (if ID is missing in ERP)
                if (!$product->woocommerce_product_id && $product->product_code) {
                    try {
                        $existingWcProducts = $woocommerce->get('products', ['sku' => $product->product_code]);
                        if (!empty($existingWcProducts)) {
                            $product->woocommerce_product_id = $existingWcProducts[0]->id;
                            $product->save();
                            Log::channel('woocommerce')->info("Matched existing product by SKU: {$product->product_code} -> ID {$product->woocommerce_product_id}");
                        }
                    } catch (\Exception $e) {
                        Log::channel('woocommerce')->warning("SKU lookup failed for {$product->product_code}: " . $e->getMessage());
                    }
                }

                // 5. Sync (with Retry Logic for Images)
                Log::channel('woocommerce')->debug("Syncing product {$product->product_code} to WC", ['payload' => $data]);

                try {
                    if ($product->woocommerce_product_id) {
                        $response = $woocommerce->put('products/' . $product->woocommerce_product_id, $data);
                        Log::channel('woocommerce')->info("Product updated in WC: ID {$product->woocommerce_product_id}");
                    } else {
                        $response = $woocommerce->post('products', $data);
                        $product->woocommerce_product_id = $response->id;
                        $product->save();
                        Log::channel('woocommerce')->info("Product created in WC: ID {$response->id}");
                    }
                    $results['synced_ids'][] = $product->woocommerce_product_id;
                } catch (\Exception $e) {
                    // If failed and data had images, try one more time WITHOUT images
                    if (isset($data['images']) && !empty($data['images'])) {
                        Log::channel('woocommerce')->warning("Sync failed with images, retrying without images for {$product->product_code}");
                        unset($data['images']);
                        
                        if ($product->woocommerce_product_id) {
                            $response = $woocommerce->put('products/' . $product->woocommerce_product_id, $data);
                        } else {
                            $response = $woocommerce->post('products', $data);
                            $product->woocommerce_product_id = $response->id;
                            $product->save();
                        }
                        $results['synced_ids'][] = $product->woocommerce_product_id;
                    } else {
                        // Re-throw if it wasn't an image issue or already tried without images
                        throw $e;
                    }
                }
            } catch (\Exception $e) {
                $errorMsg = "WC Product Sync Error for {$product->product_code}: " . $e->getMessage();
                Log::channel('woocommerce')->error($errorMsg);
                $results['errors'][] = $errorMsg;
            }
        }

        return $results;
    }

    /**
     * Helper: Get or Create WooCommerce Category by Name
     */
    private function getOrCreateCategory($woocommerce, $categoryName)
    {
        try {
            // Search for category
            $categories = $woocommerce->get('products/categories', ['search' => $categoryName]);
            
            foreach ($categories as $cat) {
                if (strtolower($cat->name) === strtolower($categoryName)) {
                    return [$cat->id];
                }
            }

            // Not found, create it
            $newCat = $woocommerce->post('products/categories', ['name' => $categoryName]);
            return [$newCat->id];
        } catch (\Exception $e) {
            Log::channel('woocommerce')->warning("Category sync failed for {$categoryName}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync Orders from WooCommerce to ERP
     */
    public function syncOrders()
    {
        $woocommerce = $this->getClient();
        $orders = $woocommerce->get('orders', ['status' => 'processing']);
        $syncedIds = [];

        foreach ($orders as $order) {
            try {
                // Check if order already exists
                $existingSale = Sale::where('woocommerce_order_id', $order->id)->first();
                if ($existingSale) continue;

                DB::transaction(function () use ($order, &$syncedIds) {
                    // Find or create customer (Safe matching)
                    $customer = null;
                    $billingEmail = $order->billing->email ?? null;
                    $billingPhone = $order->billing->phone ?? null;

                    if ($billingEmail || $billingPhone) {
                        $query = Customer::query();
                        if ($billingEmail) $query->where('email', $billingEmail);
                        if ($billingPhone) $query->orWhere('mobile', $billingPhone);
                        $customer = $query->first();
                    }

                    if (!$customer) {
                        $customer = Customer::create([
                            'name' => ($order->billing->first_name ?? 'WC') . ' ' . ($order->billing->last_name ?? 'Customer'),
                            'email' => $billingEmail,
                            'mobile' => $billingPhone,
                            'address' => $order->billing->address_1 ?? '',
                            'city' => $order->billing->city ?? '',
                            'state' => $order->billing->state ?? '',
                            'pincode' => $order->billing->postcode ?? '',
                            'woocommerce_customer_id' => $order->customer_id
                        ]);
                    }

                    // Create Sale
                    $saleDate = now();
                    if (!empty($order->date_created)) {
                        try {
                            $saleDate = \Carbon\Carbon::parse($order->date_created);
                        } catch (\Exception $e) {}
                    }

                    $sale = Sale::create([
                        'customer_id' => $customer->id,
                        'invoice_no' => 'WC-' . ($order->number ?? $order->id),
                        'sale_date' => $saleDate,
                        'sub_total' => $order->total - $order->total_tax,
                        'tax_amount' => $order->total_tax,
                        'grand_total' => $order->total,
                        'payment_status' => $order->status == 'completed' ? 'paid' : 'pending',
                        'shipping_address' => $order->shipping->address_1,
                        'city' => $order->shipping->city,
                        'state' => $order->shipping->state,
                        'pincode' => $order->shipping->postcode,
                        'receiver_name' => $order->shipping->first_name . ' ' . $order->shipping->last_name,
                        'receiver_phone' => $order->billing->phone,
                        'woocommerce_order_id' => $order->id
                    ]);

                    // Add Sale Items
                    foreach ($order->line_items as $item) {
                        $product = Product::where('woocommerce_product_id', $item->product_id)
                            ->orWhere('product_code', $item->sku)
                            ->first();

                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product ? $product->id : null,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->total,
                        ]);

                        // Update stock if product exists
                        if ($product) {
                            $product->decrement('quantity', $item->quantity);
                        }
                    }

                    $syncedIds[] = $order->id;
                });
            } catch (\Exception $e) {
                Log::channel('woocommerce')->error("WC Order Sync Error for Order #{$order->number}: " . $e->getMessage());
            }
        }

        return $syncedIds;
    }
}
