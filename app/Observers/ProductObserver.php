<?php

namespace App\Observers;

use App\Models\Product;
use App\Utils\WoocommerceUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    protected $woocommerceUtil;

    public function __construct(WoocommerceUtil $woocommerceUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
    }

    /**
     * Handle the Product "saved" event.
     */
    public function saved(Product $product)
    {
        try {
            $settings = DB::table('woocommerce_settings')->first();
            
            // Check if global auto-sync is enabled
            if ($settings && ($settings->is_sync_enabled ?? false)) {
                // Check if this specific product has sync disabled
                if (!$product->woocommerce_sync_disabled) {
                    Log::info("Auto-syncing product: {$product->name}");
                    $this->woocommerceUtil->syncProducts($product->id);
                }
            }
        } catch (\Exception $e) {
            Log::error("Auto-sync observer error: " . $e->getMessage());
        }
    }
}
