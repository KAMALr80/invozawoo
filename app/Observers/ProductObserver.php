<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\WoocommerceSetting;
use App\Services\WoocommerceService;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product)
    {
        $this->syncToWoocommerce($product, 'created');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product)
    {
        $this->syncToWoocommerce($product, 'updated');
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product)
    {
        // For now, we don't automatically delete from WC to avoid accidental data loss
    }

    /**
     * Common sync logic
     */
    protected function syncToWoocommerce(Product $product, $event)
    {
        try {
            $settings = WoocommerceSetting::getSettings();
            
            // Only sync if auto-sync is enabled and product is active
            if ($settings->auto_sync && $product->is_active) {
                $service = new WoocommerceService();
                if ($service->checkConnection()) {
                    $service->syncProduct($product);
                    Log::info("WooCommerce Auto-Sync: Product {$product->product_code} synced successfully on {$event}.");
                }
            }
        } catch (\Exception $e) {
            Log::error("WooCommerce Auto-Sync Error ({$event}): " . $e->getMessage());
        }
    }
}
