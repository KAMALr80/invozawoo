<?php
// app/Console/Commands/FixWalletSource.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\CustomerWallet;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class FixWalletSource extends Command
{
    protected $signature = 'wallet:fix-source';
    protected $description = 'Fix missing source_wallet_id for ADVANCE_USED payments';

    public function handle()
    {
        $this->info('🔍 Fixing missing source_wallet_id for ADVANCE_USED payments...');

        // Get all ADVANCE_USED payments with NULL source_wallet_id
        $payments = Payment::where('remarks', 'ADVANCE_USED')
            ->whereNull('source_wallet_id')
            ->get();

        if ($payments->isEmpty()) {
            $this->info('✅ No payments need fixing! All source_wallet_id are properly set.');
            return Command::SUCCESS;
        }

        $this->info("Found {$payments->count()} payment(s) with missing source_wallet_id");

        $fixedCount = 0;
        $failedCount = 0;

        foreach ($payments as $payment) {
            $this->line("-----------------------------------");
            $this->line("Processing Payment ID: {$payment->id}");
            $this->line("Customer ID: {$payment->customer_id}");
            $this->line("Amount: ₹{$payment->amount}");

            // Find the credit wallet from the same customer that was created BEFORE this payment
            // and has enough available balance
            $creditWallet = CustomerWallet::where('customer_id', $payment->customer_id)
                ->where('type', 'credit')
                ->where('created_at', '<', $payment->created_at)  // Created before this payment
                ->orderBy('created_at', 'asc')  // FIFO - oldest first
                ->get()
                ->filter(function($wallet) {
                    // Calculate how much of this wallet has already been used
                    $usedAmount = Payment::where('source_wallet_id', $wallet->id)
                        ->where('remarks', 'ADVANCE_USED')
                        ->sum('amount');

                    return ($wallet->amount - $usedAmount) > 0;  // Has available balance
                })
                ->first();

            if ($creditWallet) {
                // Calculate available balance
                $usedAmount = Payment::where('source_wallet_id', $creditWallet->id)
                    ->where('remarks', 'ADVANCE_USED')
                    ->sum('amount');

                $available = $creditWallet->amount - $usedAmount;

                if ($available >= $payment->amount) {
                    // This wallet can cover the full amount
                    $payment->source_wallet_id = $creditWallet->id;
                    $payment->save();

                    $this->info("✅ Fixed: Payment {$payment->id} -> source_wallet_id = {$creditWallet->id} (Available: ₹{$available})");
                    $fixedCount++;
                } else {
                    $this->warn("⚠️ Credit wallet {$creditWallet->id} only has ₹{$available} available, but payment needs ₹{$payment->amount}");
                    $failedCount++;
                }
            } else {
                $this->warn("❌ No suitable credit wallet found for Payment {$payment->id}");
                $failedCount++;
            }
        }

        $this->line("-----------------------------------");
        $this->info("📊 Summary:");
        $this->info("   Total payments processed: " . ($fixedCount + $failedCount));
        $this->info("   ✅ Fixed: {$fixedCount}");
        $this->info("   ❌ Failed: {$failedCount}");

        if ($failedCount > 0) {
            $this->warn("⚠️ Failed payments need manual review!");
        } else {
            $this->info("✅ All payments fixed successfully!");
        }

        return Command::SUCCESS;
    }
}
