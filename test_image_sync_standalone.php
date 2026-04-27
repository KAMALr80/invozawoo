<?php
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
    $dir = 'storage/app/public/woocommerce_temp';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $filename = 'test_sync_' . time() . '.jpg';
    $path = $dir . '/' . $filename;
    file_put_contents($path, $content);
    
    echo "File saved to: $path\n";
    
    $publicPath = 'public/storage/woocommerce_temp/' . $filename;
    if (file_exists($publicPath)) {
        echo "SUCCESS: File is accessible via public link!\n";
    } else {
        echo "ERROR: Storage link might be broken. File not found in public/storage.\n";
    }
} else {
    echo "ERROR: Could not download image.\n";
}
