<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditMemoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_memo_id',
        'sale_item_id',
        'product_id',
        'quantity',
        'unit_price',
        'tax_amount',
        'discount_amount',
        'total'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /* ==================== RELATIONSHIPS ==================== */

    public function creditMemo()
    {
        return $this->belongsTo(CreditMemo::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
