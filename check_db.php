<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$product = \DB::table('products')->whereNotNull('image')->first();
echo "ID: " . $product->id . "\n";
echo "Name: " . $product->name . "\n";
echo "Image: " . $product->image . "\n";
