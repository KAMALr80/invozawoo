<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

$testUrl = "https://th.bing.com/th/id/OIP.LGJUfMxoGu3sNYi2DfPP9QHaHa?w=165&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7";
echo "Testing Download from: $testUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$content = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $code\n";
echo "Content Length: " . strlen($content) . " bytes\n";

if ($content && $code === 200) {
    $filename = 'test_sync_' . time() . '.jpg';
    $path = 'public/woocommerce_temp/' . $filename;
    Storage::put($path, $content);
    
    echo "File saved to: storage/app/" . $path . "\n";
    echo "Public URL: " . asset('storage/woocommerce_temp/' . $filename) . "\n";
    
    if (file_exists(public_path('storage/woocommerce_temp/' . $filename))) {
        echo "SUCCESS: File is accessible via public link!\n";
    } else {
        echo "ERROR: Storage link might be broken. File not found in public path.\n";
    }
} else {
    echo "ERROR: Could not download image.\n";
}
