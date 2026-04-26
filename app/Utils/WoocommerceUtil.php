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
    private function uploadImageToWordPress($wc, $imagePath, $productId = null)
    {
        try {
            // Get full image URL
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $imageUrl = $imagePath;
            } else {
                $imageUrl = asset('storage/' . $imagePath);
            }

            Log::info("Uploading image: " . $imageUrl);

            // Download image temporarily
            $imageContent = @file_get_contents($imageUrl);
            if (!$imageContent) {
                throw new Exception("Cannot download image from: " . $imageUrl);
            }

            // Get image info
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageContent);
            finfo_close($finfo);

            // Determine file extension
            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg'
            };

            // Create temp file
            $tempPath = tempnam(sys_get_temp_dir(), 'woo_img_');
            file_put_contents($tempPath, $imageContent);

            // Upload using WordPress REST API
            $uploadUrl = rtrim($wc->getUrl(), '/') . '/wp-json/wp/v2/media';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($tempPath));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: ' . $mimeType,
                'Content-Disposition: attachment; filename=product_' . time() . '.' . $extension,
                'Authorization: Basic ' . base64_encode($wc->getConsumerKey() . ':' . $wc->getConsumerSecret())
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            unlink($tempPath);

            if ($httpCode === 201 || $httpCode === 200) {
                $mediaData = json_decode($response);
                if ($mediaData && isset($mediaData->id)) {
                    Log::info("Image uploaded successfully. Media ID: " . $mediaData->id);
                    return $mediaData->id;
                }
            }

            throw new Exception("Upload failed with HTTP code: " . $httpCode);

        } catch (Exception $e) {
            Log::error("Image upload error: " . $e->getMessage());
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

                // 4. Handle image upload if exists
                if ($p->image && !empty($p->image)) {
                    $mediaId = $this->uploadImageToWordPress($wc, $p->image, $p->woocommerce_product_id);
                    if ($mediaId) {
                        $data['images'] = [['id' => $mediaId]];
                        $p->woocommerce_media_id = $mediaId;
                        $p->save();
                    } elseif (str_contains($p->image, 'http')) {
                        // Fallback to direct URL
                        $data['images'] = [['src' => $p->image]];
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