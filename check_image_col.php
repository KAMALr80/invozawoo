<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$column = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM products LIKE 'image'")[0];
echo "Column: " . $column->Field . "\n";
echo "Type: " . $column->Type . "\n";

$sample = \DB::table('products')->whereNotNull('image')->first();
echo "Sample ID: " . $sample->id . "\n";
echo "Sample Image Length: " . strlen($sample->image) . "\n";
echo "Sample Image Value: " . $sample->image . "\n";
