<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmiPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'total_amount',
        'down_payment',
        'months',
        'emi_amount',
        'status',
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the sale associated with this EMI plan
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return '₹ ' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted down payment
     */
    public function getFormattedDownPaymentAttribute()
    {
        return '₹ ' . number_format($this->down_payment, 2);
    }

    /**
     * Get formatted EMI amount
     */
    public function getFormattedEmiAmountAttribute()
    {
        return '₹ ' . number_format($this->emi_amount, 2);
    }
}
