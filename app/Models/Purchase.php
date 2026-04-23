<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchases';

    protected $fillable = [
        'product_id',
        'user_id',
        'invoice_number',
        'quantity',
        'price',
        'total',
        'discount',
        'tax',
        'grand_total',
        'purchase_date',
        'payment_method',
        'payment_status',
        'supplier_name',
        'supplier_phone',
        'supplier_email',
        'notes',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity' => 'integer',
        'price' => 'float',
        'total' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'grand_total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the product that owns the purchase
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the user who created the purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Note: 'supplier' relationship doesn't exist because supplier_name is stored directly
     * We'll handle this in the controller
     */

    /* ==================== ACCESSORS ==================== */

    /**
     * Get formatted grand total
     */
    public function getFormattedGrandTotalAttribute()
    {
        return '₹ ' . number_format($this->grand_total, 2);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₹ ' . number_format($this->price, 2);
    }

    /**
     * Get formatted quantity
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->quantity) . ' units';
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'partial' => 'warning',
            'pending' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get supplier display name
     */
    public function getSupplierDisplayAttribute()
    {
        return $this->supplier_name ?? 'N/A';
    }

    /* ==================== METHODS ==================== */

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastPurchase = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPurchase && $lastPurchase->invoice_number) {
            $lastNumber = intval(substr($lastPurchase->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'INV-' . $year . $month . '-' . $newNumber;
    }

    /* ==================== BOOT ==================== */

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($purchase) {
            $purchase->total = $purchase->quantity * $purchase->price;
            $discountAmount = ($purchase->total * ($purchase->discount ?? 0)) / 100;
            $afterDiscount = $purchase->total - $discountAmount;
            $taxAmount = ($afterDiscount * ($purchase->tax ?? 0)) / 100;
            $purchase->grand_total = $afterDiscount + $taxAmount;
        });

        static::creating(function ($purchase) {
            if (empty($purchase->invoice_number)) {
                $purchase->invoice_number = self::generateInvoiceNumber();
            }
            if (empty($purchase->user_id) && Auth::check()) {
                $purchase->user_id = Auth::id();
            }
        });
    }
}
