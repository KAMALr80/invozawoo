<?php

namespace App\Utils;

use App\Models\{Product, Sale, SaleItem, Customer};
use Illuminate\Support\Facades\{DB, Log, Storage};
use Automattic\WooCommerce\Client;
use Exception;
use Illuminate\Http\Client\HttpClientException;

class WoocommerceUtil
{
    public function getClient()
    {
        $s = DB::table('woocommerce_settings')->first();
        if (!$s) throw new Exception("WooCommerce settings not configured.");

        // Ensure store URL doesn't have trailing slash
        $storeUrl = rtrim($s->store_url, '/');

        return new Client($storeUrl, $s->consumer_key, $s->consumer_secret, [
            'wp_api' => true, 
            'version' => 'wc/v3', 
            'timeout' => 120, 
            'verify_ssl' => false
        ]);
    }

    /**
     * Upload image to WordPress media library first
     */
    /**
     * Proxy Download: Download external image to ERP storage so WooCommerce can fetch it from us
     */
    private function uploadImageToWordPress($imagePath)
    {
        try {
            $s = DB::table('woocommerce_settings')->first();
            if (!$s) return null;

            $imageUrl = filter_var($imagePath, FILTER_VALIDATE_URL) ? $imagePath : asset('storage/' . $imagePath);
            
            // Download image
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if (!$imageContent || $httpCode !== 200) return null;

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageContent);
            finfo_close($finfo);

            $extension = match($mimeType) {
                'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', default => 'jpg'
            };

            $filename = 'woo_sync_' . md5($imagePath) . '.' . $extension;
            $path = 'public/woocommerce_temp/' . $filename;
            
            // Save to public storage
            Storage::put($path, $imageContent);
            
            // Return the public URL of OUR server
            return asset('storage/woocommerce_temp/' . $filename);
        } catch (Exception $e) {
            Log::error("Proxy download error: " . $e->getMessage());
            return null;
        }
    }

    public function syncProducts($productId = null)
    {
        $wc = $this->getClient();
        $cats = [];
        
        try {
            $categories = $wc->get('products/categories', ['per_page' => 100]);
            foreach ($categories as $c) {
                $cats[strtolower($c->name)] = $c->id;
            }
        } catch (Exception $e) {
            Log::warning("Could not fetch categories: " . $e->getMessage());
        }

        $query = Product::where('woocommerce_sync_disabled', 0);
        if ($productId) {
            $query->where('id', $productId);
        }
        $prods = $query->get();
        
        $res = ['synced_ids' => [], 'errors' => []];

        foreach ($prods as $p) {
            try {
                // 1. Resolve Category
                $catIds = [];
                if ($p->category && !empty($p->category)) {
                    $name = strtolower(trim($p->category));
                    if (!isset($cats[$name]) && !empty($name)) {
                        try {
                            $new = $wc->post('products/categories', ['name' => $p->category]);
                            $cats[$name] = $new->id;
                        } catch (Exception $e) {
                            Log::warning("Could not create category {$p->category}: " . $e->getMessage());
                        }
                    }
                    if (isset($cats[$name])) {
                        $catIds = [['id' => $cats[$name]]];
                    }
                }

                // 2. Check if product exists by SKU
                if (!$p->woocommerce_product_id && $p->product_code) {
                    try {
                        $existing = $wc->get('products', ['sku' => $p->product_code]);
                        if (!empty($existing)) {
                            $p->woocommerce_product_id = $existing[0]->id;
                            $p->save();
                        }
                    } catch (Exception $e) {
                        // SKU check failed, continue with creation
                    }
                }

                // 3. Prepare product data
                $data = [
                    'name' => $p->name,
                    'type' => 'simple',
                    'status' => 'publish',
                    'sku' => $p->product_code,
                    'regular_price' => (string) number_format((float)$p->price, 2, '.', ''),
                    'manage_stock' => true,
                    'stock_quantity' => (int) max(0, $p->quantity),
                    'stock_status' => $p->quantity > 0 ? 'instock' : 'outofstock',
                    'description' => $p->description ?: $p->name,
                    'short_description' => substr($p->description ?? $p->name, 0, 200),
                    'categories' => $catIds
                ];

                // 4. Handle image - Proxy Download Strategy
                if (!empty($p->image)) {
                    $imgData = [];
                    
                    // Always try to proxy download to our server first
                    $proxyUrl = $this->uploadImageToWordPress($p->image);
                    
                    if ($proxyUrl && filter_var($proxyUrl, FILTER_VALIDATE_URL)) {
                        // Use our server's URL - WooCommerce will definitely accept this
                        $imgData = [
                            'src' => $proxyUrl,
                            'name' => $p->name,
                            'alt' => $p->name
                        ];
                    } else {
                        // Fallback to original URL or local asset
                        $fallbackUrl = str_contains($p->image, 'http') ? $p->image : asset('storage/' . $p->image);
                        $imgData = [
                            'src' => $fallbackUrl,
                            'name' => $p->name
                        ];
                    }

                    if (!empty($imgData)) {
                        $data['images'] = [$imgData];
                    }
                }

                // 5. Sync product
                try {
                    if ($p->woocommerce_product_id) {
                        $resp = $wc->put('products/' . $p->woocommerce_product_id, $data);
                    } else {
                        $resp = $wc->post('products', $data);
                    }
                } catch (Exception $e) {
                    // Try without images if image upload failed
                    if (isset($data['images'])) {
                        unset($data['images']);
                        if ($p->woocommerce_product_id) {
                            $resp = $wc->put('products/' . $p->woocommerce_product_id, $data);
                        } else {
                            $resp = $wc->post('products', $data);
                        }
                    } else {
                        throw $e;
                    }
                }

                // Update product ID
                if (!$p->woocommerce_product_id && isset($resp->id)) {
                    $p->woocommerce_product_id = $resp->id;
                    $p->save();
                }

                $res['synced_ids'][] = $p->woocommerce_product_id;
                Log::info("Product synced: {$p->name} (ID: {$p->woocommerce_product_id})");

            } catch (Exception $e) {
                $errorMsg = "{$p->product_code}: " . $e->getMessage();
                $res['errors'][] = $errorMsg;
                Log::error("Product sync error: " . $errorMsg);
            }
        }
        
        return $res;
    }

    public function syncCustomers()
    {
        $wc = $this->getClient();
        $synced = [];
        
        $customers = Customer::whereNull('woocommerce_customer_id')->get();
        
        foreach ($customers as $c) {
            try {
                $parts = explode(' ', trim($c->name), 2);
                $firstName = $parts[0];
                $lastName = $parts[1] ?? '';
                
                $customerData = [
                    'email' => $c->email ?: ($c->mobile . '@temp.com'),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $c->email ?: $c->mobile,
                    'billing' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'address_1' => $c->address ?? '',
                        'city' => $c->city ?? '',
                        'state' => $c->state ?? '',
                        'postcode' => $c->pincode ?? '',
                        'country' => $c->country ?? 'IN',
                        'email' => $c->email ?: ($c->mobile . '@temp.com'),
                        'phone' => $c->mobile ?? ''
                    ]
                ];
                
                // Check if customer exists
                try {
                    $existing = $wc->get('customers', ['email' => $c->email]);
                    if (!empty($existing)) {
                        $c->woocommerce_customer_id = $existing[0]->id;
                        $c->save();
                        $synced[] = $existing[0]->id;
                        continue;
                    }
                } catch (Exception $e) {
                    // Not found, create new
                }
                
                $resp = $wc->post('customers', $customerData);
                $c->woocommerce_customer_id = $resp->id;
                $c->save();
                $synced[] = $resp->id;
                
                Log::info("Customer synced: {$c->name} (ID: {$resp->id})");
                
            } catch (Exception $e) {
                Log::error("Customer sync error for {$c->name}: " . $e->getMessage());
            }
        }
        
        return $synced;
    }

    public function syncOrders()
    {
        $wc = $this->getClient();
        $synced = [];
        
        try {
            $orders = $wc->get('orders', ['status' => 'processing', 'per_page' => 50]);
            
            foreach ($orders as $o) {
                if (Sale::where('woocommerce_order_id', $o->id)->exists()) {
                    continue;
                }
                
                DB::transaction(function() use ($o, &$synced) {
                    // Find or create customer
                    $customer = Customer::where('email', $o->billing->email)
                        ->orWhere('mobile', $o->billing->phone)
                        ->first();
                    
                    if (!$customer) {
                        $customer = Customer::create([
                            'name' => trim($o->billing->first_name . ' ' . $o->billing->last_name),
                            'email' => $o->billing->email,
                            'mobile' => $o->billing->phone,
                            'address' => $o->billing->address_1 ?? '',
                            'city' => $o->billing->city ?? '',
                            'state' => $o->billing->state ?? '',
                            'pincode' => $o->billing->postcode ?? '',
                            'country' => $o->billing->country ?? 'IN',
                            'woocommerce_customer_id' => $o->customer_id
                        ]);
                    }
                    
                    // Create sale
                    $sale = Sale::create([
                        'customer_id' => $customer->id,
                        'invoice_no' => 'WC-' . $o->id,
                        'sale_date' => now(),
                        'sub_total' => floatval($o->subtotal),
                        'discount' => floatval($o->discount_total),
                        'tax' => floatval($o->total_tax) > 0 ? (floatval($o->total_tax) / floatval($o->subtotal)) * 100 : 0,
                        'tax_amount' => floatval($o->total_tax),
                        'grand_total' => floatval($o->total),
                        'payment_status' => 'paid',
                        'paid_amount' => floatval($o->total),
                        'requires_shipping' => !empty($o->shipping->address_1),
                        'shipping_address' => $o->shipping->address_1 ?? '',
                        'city' => $o->shipping->city ?? $o->billing->city ?? '',
                        'state' => $o->shipping->state ?? $o->billing->state ?? '',
                        'pincode' => $o->shipping->postcode ?? $o->billing->postcode ?? '',
                        'receiver_name' => trim($o->shipping->first_name . ' ' . $o->shipping->last_name),
                        'receiver_phone' => $o->shipping->phone ?? $o->billing->phone ?? '',
                        'woocommerce_order_id' => $o->id
                    ]);
                    
                    // Create sale items
                    foreach ($o->line_items as $item) {
                        $product = Product::where('woocommerce_product_id', $item->product_id)
                            ->orWhere('product_code', $item->sku)
                            ->first();
                        
                        if ($product) {
                            SaleItem::create([
                                'sale_id' => $sale->id,
                                'product_id' => $product->id,
                                'quantity' => $item->quantity,
                                'price' => floatval($item->price),
                                'total' => floatval($item->total),
                                'mrp' => floatval($item->price)
                            ]);
                            
                            // Decrement stock
                            $product->decrement('quantity', $item->quantity);
                        }
                    }
                    
                    $synced[] = $o->id;
                });
            }
        } catch (Exception $e) {
            Log::error("Order sync error: " . $e->getMessage());
        }
        
        return $synced;
    }
}