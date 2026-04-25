<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Cleaning up...\n";

Schema::table('products', function ($table) {
    if (Schema::hasColumn('products', 'woocommerce_product_id')) {
        $table->dropColumn('woocommerce_product_id');
    }
    if (Schema::hasColumn('products', 'woocommerce_media_id')) {
        $table->dropColumn('woocommerce_media_id');
    }
    if (Schema::hasColumn('products', 'woocommerce_sync_disabled')) {
        $table->dropColumn('woocommerce_sync_disabled');
    }
});

Schema::table('customers', function ($table) {
    if (Schema::hasColumn('customers', 'woocommerce_customer_id')) {
        $table->dropColumn('woocommerce_customer_id');
    }
});

Schema::table('sales', function ($table) {
    if (Schema::hasColumn('sales', 'woocommerce_order_id')) {
        $table->dropColumn('woocommerce_order_id');
    }
});

Schema::dropIfExists('woocommerce_settings');

echo "Cleanup done!\n";
