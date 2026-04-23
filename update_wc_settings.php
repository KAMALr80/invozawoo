<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WoocommerceSetting;

$settings = WoocommerceSetting::getSettings();
$settings->app_url = 'https://newdevsite.dev.brainbean.us/';
$settings->consumer_key = 'ck_decb67e576b2069ac0b4b32ada12f695f2837acc';
$settings->consumer_secret = 'cs_8db4e416ec219c8f14dad0b026d80deb79a2517e';
$settings->save();

echo "Settings Saved Successfully!\n";
echo "URL: " . $settings->app_url . "\n";
echo "Key: " . $settings->consumer_key . "\n";
