<?php

namespace App\Utils;

use App\Models\{Product, Sale, SaleItem, Customer};
use Illuminate\Support\Facades\{DB, Log};
use Automattic\WooCommerce\Client;
use Exception;

class WoocommerceUtil
{
    public function getClient()
    {
        $s = DB::table('woocommerce_settings')->first();
        if (!$s) throw new Exception("WooCommerce settings not configured.");

        return new Client($s->store_url, $s->consumer_key, $s->consumer_secret, [
            'wp_api' => true, 'version' => 'wc/v3', 'timeout' => 120, 'verify_ssl' => false
        ]);
    }

    public function syncProducts($productId = null)
    {
        $wc = $this->getClient();
        $cats = [];
        try {
            foreach ($wc->get('products/categories', ['per_page' => 100]) as $c) $cats[strtolower($c->name)] = $c->id;
        } catch (Exception $e) {}

        $prods = Product::where('woocommerce_sync_disabled', 0)->when($productId, fn($q) => $q->where('id', $productId))->get();
        $res = ['synced_ids' => [], 'errors' => []];

        foreach ($prods as $p) {
            try {
                // 1. Resolve Category
                $catIds = [];
                if ($p->category) {
                    $name = strtolower($p->category);
                    if (!isset($cats[$name])) {
                        $new = $wc->post('products/categories', ['name' => $p->category]);
                        $cats[$name] = $new->id;
                    }
                    $catIds = [['id' => $cats[$name]]];
                }

                // 2. Map Data
                $data = [
                    'name' => $p->name, 'type' => 'simple', 'status' => 'publish', 'sku' => $p->product_code,
                    'regular_price' => (string)$p->price, 'manage_stock' => true, 'stock_quantity' => (int)$p->quantity,
                    'stock_status' => $p->quantity > 0 ? 'instock' : 'outofstock',
                    'description' => $p->description ?: $p->name, 'categories' => $catIds
                ];

                if ($p->image && !str_contains($url = asset('storage/'.$p->image), 'localhost')) {
                    $data['images'] = [['src' => $url]];
                }

                // 3. Match SKU if no ID
                if (!$p->woocommerce_product_id) {
                    $existing = $wc->get('products', ['sku' => $p->product_code]);
                    if (!empty($existing)) { $p->woocommerce_product_id = $existing[0]->id; $p->save(); }
                }

                // 4. Sync with Image Fallback
                try {
                    $resp = $p->woocommerce_product_id ? $wc->put('products/'.$p->woocommerce_product_id, $data) : $wc->post('products', $data);
                } catch (Exception $e) {
                    if (isset($data['images'])) {
                        unset($data['images']);
                        $resp = $p->woocommerce_product_id ? $wc->put('products/'.$p->woocommerce_product_id, $data) : $wc->post('products', $data);
                    } else throw $e;
                }

                if (!$p->woocommerce_product_id) { $p->woocommerce_product_id = $resp->id; $p->save(); }
                $res['synced_ids'][] = $p->woocommerce_product_id;

            } catch (Exception $e) { $res['errors'][] = "{$p->product_code}: ".$e->getMessage(); }
        }
        return $res;
    }

    public function syncCustomers()
    {
        $wc = $this->getClient();
        $synced = [];
        foreach (Customer::whereNull('woocommerce_customer_id')->get() as $c) {
            try {
                $parts = explode(' ', $c->name);
                $resp = $wc->post('customers', [
                    'email' => $c->email, 'first_name' => $parts[0], 'last_name' => $parts[1] ?? '',
                    'username' => $c->mobile ?: $c->email,
                    'billing' => ['first_name' => $parts[0], 'last_name' => $parts[1] ?? '', 'address_1' => $c->address, 'city' => $c->city, 'phone' => $c->mobile, 'country' => 'IN']
                ]);
                $c->update(['woocommerce_customer_id' => $resp->id]);
                $synced[] = $resp->id;
            } catch (Exception $e) { Log::error("WC Cust Error: ".$e->getMessage()); }
        }
        return $synced;
    }

    public function syncOrders()
    {
        $wc = $this->getClient();
        $synced = [];
        try {
            foreach ($wc->get('orders', ['status' => 'processing']) as $o) {
                if (Sale::where('woocommerce_order_id', $o->id)->exists()) continue;
                DB::transaction(function() use ($o, &$synced) {
                    $c = Customer::where('email', $o->billing->email)->orWhere('mobile', $o->billing->phone)->first() ?: Customer::create([
                        'name' => $o->billing->first_name.' '.$o->billing->last_name, 'email' => $o->billing->email, 'mobile' => $o->billing->phone,
                        'address' => $o->billing->address_1, 'city' => $o->billing->city, 'woocommerce_customer_id' => $o->customer_id
                    ]);
                    $s = Sale::create([
                        'customer_id' => $c->id, 'invoice_no' => 'WC-'.$o->id, 'sale_date' => now(), 'grand_total' => $o->total,
                        'woocommerce_order_id' => $o->id, 'payment_status' => 'paid', 'shipping_address' => $o->shipping->address_1
                    ]);
                    foreach ($o->line_items as $i) {
                        $p = Product::where('woocommerce_product_id', $i->product_id)->orWhere('product_code', $i->sku)->first();
                        SaleItem::create(['sale_id' => $s->id, 'product_id' => $p?->id, 'quantity' => $i->quantity, 'price' => $i->price, 'total' => $i->total]);
                        $p?->decrement('quantity', $i->quantity);
                    }
                    $synced[] = $o->id;
                });
            }
        } catch (Exception $e) { Log::error("WC Order Error: ".$e->getMessage()); }
        return $synced;
    }
}
