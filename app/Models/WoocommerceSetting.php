<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoocommerceSetting extends Model
{
    protected $fillable = [
        'app_url',
        'consumer_key',
        'consumer_secret',
        'auto_sync',
        'default_tax_class',
        'sync_price_type',
        'product_fields',
        'last_sync_stats'
    ];

    protected $casts = [
        'auto_sync' => 'boolean',
        'product_fields' => 'json',
        'last_sync_stats' => 'json'
    ];

    /**
     * Get the singleton settings record
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'product_fields' => ['name', 'price', 'category', 'quantity', 'images', 'description'],
            'last_sync_stats' => []
        ]);
    }
}
