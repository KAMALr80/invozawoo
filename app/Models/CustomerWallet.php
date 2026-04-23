<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',        // credit | debit
        'amount',
        'balance',
        'reference'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the customer that owns this wallet transaction
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get payments that used this as source
     */
    public function sourcePayments()
    {
        return $this->hasMany(Payment::class, 'source_wallet_id');
    }

    /**
     * Get payments linked to this wallet
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'wallet_id');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₹ ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute()
    {
        return '₹ ' . number_format($this->balance, 2);
    }
}
