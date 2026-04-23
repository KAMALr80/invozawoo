<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'total'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /* ==================== ACCESSORS ==================== */

    public function getFormattedPriceAttribute()
    {
        return '₹ ' . number_format($this->price, 2);
    }

    public function getFormattedTotalAttribute()
    {
        return '₹ ' . number_format($this->total, 2);
    }

    public function getProductNameAttribute()
    {
        return $this->product->name ?? 'Deleted Product';
    }
}
