<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\CustomerWallet;
use App\Models\EmiPlan;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public static function handle(Sale $sale, array $data)
    {
        return DB::transaction(function () use ($sale, $data) {

            $paidAmount = $data['paid_amount'];
            $total = $sale->grand_total;
            $customerId = $sale->customer_id;

            /* ======================
               1. SAVE PAYMENT
            ====================== */

            Payment::create([
                'sale_id' => $sale->id,
                'amount' => $paidAmount,
                'method' => $data['method'],
                'status' => 'paid',
                'transaction_id' => $data['transaction_id'] ?? null,
            ]);

            /* ======================
               2. FULL PAYMENT
            ====================== */

            if ($paidAmount == $total) {
                $sale->update(['payment_status' => 'paid']);
                return;
            }

            /* ======================
               3. PARTIAL → EMI
            ====================== */

            if ($paidAmount < $total) {

                $remaining = $total - $paidAmount;

                EmiPlan::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customerId,
                    'total_amount' => $remaining,
                    'monthly_amount' => $data['emi_amount'],
                    'months' => $data['emi_months'],
                    'status' => 'active'
                ]);

                $sale->update(['payment_status' => 'emi']);
                return;
            }

            /* ======================
               4. EXTRA → WALLET
            ====================== */

            if ($paidAmount > $total) {

                $extra = $paidAmount - $total;

                CustomerWallet::updateOrCreate(
                    ['customer_id' => $customerId],
                    ['balance' => DB::raw("balance + $extra")]
                );

                $sale->update(['payment_status' => 'paid']);
            }
        });
    }
}
