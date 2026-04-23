<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\WoocommerceService;
use Illuminate\Support\Facades\Log;

$service = new WoocommerceService();
echo "Checking Connection...\n";
if ($service->checkConnection()) {
    echo "Connection Successful!\n";
    
    $product = Product::where('is_active', true)->first();
    if ($product) {
        echo "Syncing Product: " . $product->name . " (SKU: " . $product->product_code . ")\n";
        $response = $service->syncProduct($product);
        
        if ($response === true || (is_object($response) && $response->successful())) {
            echo "Sync Successful!\n";
        } else {
            echo "Sync Failed!\n";
            if (is_object($response)) {
                echo "Response: " . $response->body() . "\n";
            }
        }
    } else {
        echo "No active products found.\n";
    }
} else {
    echo "Connection Failed! Check credentials.\n";
}
