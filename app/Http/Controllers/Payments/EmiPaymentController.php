<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\EmiPlan;
use App\Models\Payment;

class EmiPaymentController extends Controller
{
    /* ============================
       EMI PAYMENT PAGE
    ============================ */
    public function show(EmiPlan $emi)
    {
        if ($emi->status !== 'running') {
            return redirect()
                ->route('sales.show', $emi->sale_id)
                ->with('error', 'EMI already completed');
        }

        $sale     = $emi->sale;
        $customer = $sale->customer;

        $openBalance = $customer->open_balance ?? 0;

        $remaining = $emi->emi_amount * $emi->months;
        $payAmount = min($emi->emi_amount, $remaining);

        return view('emi.pay', compact(
            'emi',
            'remaining',
            'payAmount',
            'openBalance'
        ));
    }

    /* ============================
       PAY EMI INSTALLMENT
    ============================ */
    public function pay(EmiPlan $emi)
    {
        if ($emi->status !== 'running') {
            return redirect()
                ->route('sales.show', $emi->sale_id)
                ->with('error', 'EMI already completed');
        }

        DB::transaction(function () use ($emi) {

            $sale     = $emi->sale;
            $customer = $sale->customer;

            $emiAmount = $emi->emi_amount;

            /*
            |--------------------------------------------------
            | STEP 1: CHECK ADVANCE
            |--------------------------------------------------
            */
            $openBalance = $customer->open_balance; // +due, -advance
            $advanceAvailable = $openBalance < 0 ? abs($openBalance) : 0;

            /*
            |--------------------------------------------------
            | STEP 2: SPLIT PAYMENT (ADVANCE + CASH)
            |--------------------------------------------------
            */
            $advanceUsed = min($advanceAvailable, $emiAmount);
            $cashToPay   = $emiAmount - $advanceUsed;

            // 🔹 record advance usage (NO balance change here)
            if ($advanceUsed > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount'  => $advanceUsed,
                    'method'  => 'advance',
                    'status'  => 'paid',
                ]);
            }

            // 🔹 record EMI cash payment
            if ($cashToPay > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount'  => $cashToPay,
                    'method'  => 'emi',
                    'status'  => 'paid',
                ]);
            }

            /*
            |--------------------------------------------------
            | STEP 3: 🔥 REDUCE DUE ONLY ONCE
            |--------------------------------------------------
            */
            $customer->open_balance -= $emiAmount;

            /*
            |--------------------------------------------------
            | STEP 4: UPDATE EMI PLAN
            |--------------------------------------------------
            */
            $emi->months--;

            if ($emi->months <= 0) {

                $emi->status = 'completed';

                // EMI finished → balance must be clean
                $customer->open_balance = 0;
                $sale->payment_status  = 'paid';

            } else {

                $emi->status = 'running';
                $sale->payment_status  = 'emi';
            }

            $emi->save();
            $sale->save();
            $customer->save();
        });

        // ✅ redirect to invoice
        return redirect()
            ->route('sales.show', $emi->sale_id)
            ->with('success', 'EMI installment paid successfully');
    }
}
