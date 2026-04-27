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
        // Run sync AFTER the response has been sent to the user for maximum speed
        dispatch(function() use ($product) {
            try {
                $settings = DB::table('woocommerce_settings')->first();
                
                if ($settings && ($settings->is_sync_enabled ?? false)) {
                    if (!$product->woocommerce_sync_disabled) {
                        Log::info("Background auto-syncing product: {$product->name}");
                        $this->woocommerceUtil->syncProducts($product->id);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Auto-sync observer error: " . $e->getMessage());
            }
        })->afterResponse();
    }
}
