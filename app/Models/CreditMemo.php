<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class CreditMemo extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'cm_number',
        'sale_id',
        'customer_id',
        'type',
        'status',
        'sub_total',
        'tax_amount',
        'refund_amount',
        'reason',
        'restock',
        'refund_method',
        'created_by'
    ];

    protected $casts = [
        'sub_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'restock' => 'boolean',
    ];

    /* ==================== RELATIONSHIPS ==================== */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CreditMemoItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ==================== METHODS ==================== */

    public static function generateCMNumber()
    {
        $prefix = 'CM-' . date('Y') . '-';
        $last = self::where('cm_number', 'LIKE', $prefix . '%')->latest()->first();
        $number = $last ? intval(substr($last->cm_number, strrpos($last->cm_number, '-') + 1)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
