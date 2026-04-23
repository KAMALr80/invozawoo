<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'remarks',
        'payment_date',
        'wallet_id',           // Link to wallet transaction
        'source_wallet_id',    // For tracking which advance was used
        'emi_months',           // Number of EMI months
        'down_payment',         // Down payment amount for EMI
        'emi_amount'           // Monthly EMI amount
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'emi_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the sale associated with this payment
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the customer associated with this payment
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the wallet transaction associated with this payment
     */
    public function wallet()
    {
        return $this->belongsTo(CustomerWallet::class);
    }

    /**
     * Get the source wallet transaction (for advance used)
     */
    public function sourceWallet()
    {
        return $this->belongsTo(CustomerWallet::class, 'source_wallet_id');
    }

    /**
     * Get all payments that used this advance (for ADVANCE_ONLY)
     * यह वह रिलेशनशिप है जो canBeDeleted() में इस्तेमाल हो रहा है
     */
    public function advanceUsages()
    {
        return $this->hasMany(Payment::class, 'source_wallet_id', 'wallet_id')
                    ->where('remarks', 'ADVANCE_USED');
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope for EMI payments
     */
    public function scopeEmi($query)
    {
        return $query->where('method', 'emi');
    }

    /**
     * Scope for invoice payments (not advance, not excess)
     */
    public function scopeInvoicePayments($query)
    {
        return $query->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'EMI_MONTHLY']);
    }

    /**
     * Scope for advance payments (pure advance)
     */
    public function scopeAdvancePayments($query)
    {
        return $query->where('remarks', 'ADVANCE_ONLY');
    }

    /**
     * Scope for advance used payments
     */
    public function scopeAdvanceUsed($query)
    {
        return $query->where('remarks', 'ADVANCE_USED');
    }

    /**
     * Scope for excess payments (went to advance)
     */
    public function scopeExcessPayments($query)
    {
        return $query->where('remarks', 'EXCESS_TO_ADVANCE');
    }

    /**
     * Scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for cancelled payments
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get payments by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /* ==================== CHECK METHODS ==================== */

    /**
     * Check if payment is for invoice
     */
    public function isInvoicePayment()
    {
        return in_array($this->remarks, ['INVOICE', 'EMI_DOWN', 'EMI_MONTHLY']);
    }

    /**
     * Check if payment is pure advance
     */
    public function isAdvancePayment()
    {
        return $this->remarks === 'ADVANCE_ONLY';
    }

    /**
     * Check if payment used advance from wallet
     */
    public function isAdvanceUsed()
    {
        return $this->remarks === 'ADVANCE_USED';
    }

    /**
     * Check if payment is excess (went to advance)
     */
    public function isExcessPayment()
    {
        return $this->remarks === 'EXCESS_TO_ADVANCE';
    }

    /**
     * Check if this is an EMI payment
     */
    public function isEmi()
    {
        return $this->method === 'emi' || in_array($this->remarks, ['EMI_DOWN', 'EMI_MONTHLY']);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get payment method icon
     */
    public function getMethodIconAttribute()
    {
        return match ($this->method) {
            'cash' => '💵',
            'upi' => '📱',
            'card' => '💳',
            'net_banking' => '🏦',
            'emi' => '📆',
            'advance' => '👛',
            default => '💰'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'paid' => 'green',
            'cancelled' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get remarks badge color
     */
    public function getRemarksColorAttribute()
    {
        return match ($this->remarks) {
            'INVOICE', 'EMI_DOWN', 'EMI_MONTHLY' => '#059669',
            'ADVANCE_ONLY' => '#f59e0b',
            'ADVANCE_USED' => '#8b5cf6',
            'EXCESS_TO_ADVANCE' => '#3b82f6',
            default => '#6b7280'
        };
    }

    /**
     * Get remarks display text
     */
    public function getRemarksDisplayAttribute()
    {
        return match ($this->remarks) {
            'INVOICE' => 'Invoice Payment',
            'EMI_DOWN' => 'EMI Down Payment',
            'EMI_MONTHLY' => 'Monthly EMI',
            'ADVANCE_ONLY' => 'Pure Advance',
            'ADVANCE_USED' => 'Advance Used',
            'EXCESS_TO_ADVANCE' => 'Excess to Advance',
            default => $this->remarks ?? 'Payment'
        };
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₹ ' . number_format($this->amount, 2);
    }

    /**
     * Get payment date formatted
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y - h:i A');
    }

    /**
     * Get short date
     */
    public function getShortDateAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    /**
     * Get time
     */
    public function getTimeAttribute()
    {
        return $this->created_at->format('h:i A');
    }

    /**
     * Get wallet balance after this payment
     */
    public function getWalletBalanceAfterAttribute()
    {
        if ($this->wallet) {
            return $this->wallet->balance;
        }
        return null;
    }

    /**
     * Get formatted down payment
     */
    public function getFormattedDownPaymentAttribute()
    {
        return '₹ ' . number_format($this->down_payment ?? 0, 2);
    }

    /**
     * Get formatted EMI amount
     */
    public function getFormattedEmiAmountAttribute()
    {
        return '₹ ' . number_format($this->emi_amount ?? 0, 2);
    }

    /* ==================== METHODS ==================== */

    /**
     * Check if this payment can be deleted
     */
    public function canBeDeleted()
    {
        // If this is an advance payment that has been used elsewhere, warn but allow
        if ($this->isAdvancePayment()) {
            $usageCount = $this->advanceUsages()->count();
            if ($usageCount > 0) {
                return [
                    'allowed' => true,
                    'warning' => "This advance has been used in {$usageCount} other invoice(s). Deleting it will affect those invoices!"
                ];
            }
        }

        // If this is an advance usage, warn but allow
        if ($this->isAdvanceUsed()) {
            return [
                'allowed' => true,
                'warning' => "This payment used advance from another payment. Deleting it will restore that advance!"
            ];
        }

        return ['allowed' => true, 'warning' => null];
    }

    /**
     * Get the number of times this advance has been used
     */
    public function getAdvanceUsageCountAttribute()
    {
        if ($this->isAdvancePayment()) {
            return $this->advanceUsages()->count();
        }
        return 0;
    }

    /**
     * Get the total amount used from this advance
     */
    public function getAdvanceUsedAmountAttribute()
    {
        if ($this->isAdvancePayment() && $this->wallet) {
            return $this->advanceUsages()->sum('amount');
        }
        return 0;
    }

    /**
     * Get remaining advance amount
     */
    public function getAdvanceRemainingAttribute()
    {
        if ($this->isAdvancePayment()) {
            return $this->amount - $this->advance_used_amount;
        }
        return 0;
    }

    /* ==================== BOOT ==================== */

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($payment) {
            // 🔥 FIX: EXCESS_TO_ADVANCE, ADVANCE_ONLY, WALLET_ADD ke liye
            if (in_array($payment->remarks, ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])
                && $payment->wallet_id
                && !$payment->source_wallet_id) {

                $payment->source_wallet_id = $payment->wallet_id;
                Log::info("✅ Auto-set source_wallet_id for {$payment->remarks} to {$payment->wallet_id}");
            }

            // Automatically set source_wallet_id for ADVANCE_USED if missing
            if ($payment->remarks === 'ADVANCE_USED' && !$payment->source_wallet_id) {
                $creditWallet = CustomerWallet::where('customer_id', $payment->customer_id)
                    ->where('type', 'credit')
                    ->whereRaw('amount > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE source_wallet_id = customer_wallets.id AND remarks = "ADVANCE_USED")')
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->first();

                if ($creditWallet) {
                    $payment->source_wallet_id = $creditWallet->id;
                    Log::info("✅ Model set source_wallet_id for ADVANCE_USED to {$creditWallet->id}");
                }
            }
        });

        static::deleting(function ($payment) {
            // Log when payment is being deleted
            Log::info("⚠️ Payment being deleted: ID {$payment->id}, Amount: {$payment->amount}, Remarks: {$payment->remarks}");
        });
    }
}
