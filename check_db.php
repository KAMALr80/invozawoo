<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\WoocommerceSetting;

echo "Total Products: " . Product::count() . "\n";
echo "Active Products: " . Product::where('is_active', true)->count() . "\n";
$settings = WoocommerceSetting::first();
if ($settings) {
    echo "WC URL: " . $settings->app_url . "\n";
    echo "WC Key: " . substr($settings->consumer_key, 0, 10) . "...\n";
} else {
    echo "No WC Settings found.\n";
}
