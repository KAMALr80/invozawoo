<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoocommerceSyncLog extends Model
{
    protected $fillable = [
        'operation_type',
        'items_total',
        'items_success',
        'items_failed',
        'status',
        'details'
    ];

    protected $casts = [
        'details' => 'json'
    ];
}
