<?php
// app/Http/Controllers/CustomerWalletController.php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerWallet;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerWalletController extends Controller
{
    /**
     * Add money to wallet (credit) - Can be from index or invoice page
     */
    public function addAdvance(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:1|max:9999999.99',
            'method'      => 'nullable|in:cash,upi,card,net_banking,bank_transfer',
            'sale_id'     => 'nullable|exists:sales,id',
            'reference'   => 'nullable|string|max:255',
            'remarks'     => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::lockForUpdate()->findOrFail($request->customer_id);

            // Get current wallet balance
            $currentBalance = $this->getCurrentWalletBalance($customer->id);
            $newBalance = $currentBalance + $request->amount;

            // Create wallet entry (credit)
            $wallet = new CustomerWallet();
            $wallet->customer_id = $customer->id;
            $wallet->type        = 'credit';
            $wallet->amount      = $request->amount;
            $wallet->balance     = $newBalance;
            $wallet->reference   = $request->remarks ?? ($request->sale_id ? 'Added from invoice' : 'Wallet add');
            $wallet->save();

            // Create payment record
            $payment = new Payment();
            $payment->customer_id    = $customer->id;
            $payment->sale_id        = $request->sale_id;
            $payment->amount         = $request->amount;
            $payment->method         = $request->method ?? 'cash';
            $payment->transaction_id = $request->reference;
            $payment->remarks        = 'WALLET_ADD';
            $payment->status         = 'paid';
            $payment->wallet_id      = $wallet->id;
            $payment->save();

            // Update customer's wallet_balance
            $customer->wallet_balance = $newBalance;
            $customer->save();

            DB::commit();

            // If added from invoice, recalculate that invoice
            if ($request->sale_id) {
                $this->recalculateInvoiceStatus($request->sale_id);
            }

            $plainMessage = '₹' . number_format($request->amount, 2) . ' added to wallet! New balance: ₹' . number_format($newBalance, 2);
            $htmlMessage  = '💰 ₹' . number_format($request->amount, 2) . ' added to wallet successfully!<br>Current wallet balance: ₹' . number_format($newBalance, 2);

            // Return JSON for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'     => true,
                    'message'     => '✅ ' . $plainMessage,
                    'new_balance' => $newBalance,
                ]);
            }

            return redirect()->back()->with('success', $htmlMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet Add Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace'   => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Error: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Error adding to wallet: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Use money from wallet (debit) - Can be linked to invoice or independent
     */
    public function useAdvance(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:1|max:9999999.99',
            'sale_id'     => 'nullable|exists:sales,id',
            'remarks'     => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::lockForUpdate()->findOrFail($request->customer_id);

            // Get current wallet balance
            $currentBalance = $this->getCurrentWalletBalance($customer->id);

            // Check balance before processing
            if ($currentBalance < $request->amount) {
                $msg = 'Insufficient wallet balance! Available: ₹' . number_format($currentBalance, 2);
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => '❌ ' . $msg], 422);
                }
                throw new \Exception($msg);
            }

            $newBalance = $currentBalance - $request->amount;

            // Apply FIFO: Find which wallet credit entries this debit is using
            $sourceWalletId = $this->applyFIFO($customer->id, $request->amount);

            // Create wallet entry (debit)
            $wallet = new CustomerWallet();
            $wallet->customer_id = $customer->id;
            $wallet->type        = 'debit';
            $wallet->amount      = $request->amount;
            $wallet->balance     = $newBalance;
            $wallet->reference   = $request->remarks ?? ($request->sale_id ? 'Used for invoice' : 'Wallet withdrawal');
            $wallet->save();

            // Create payment record
            $payment = new Payment();
            $payment->customer_id     = $customer->id;
            $payment->sale_id         = $request->sale_id;
            $payment->amount          = $request->amount;
            $payment->method          = 'wallet';
            $payment->remarks         = 'ADVANCE_USED';
            $payment->status          = 'paid';
            $payment->wallet_id       = $wallet->id;
            $payment->source_wallet_id = $sourceWalletId;
            $payment->save();

            // Update customer wallet_balance
            $customer->wallet_balance = $newBalance;
            $customer->save();

            DB::commit();

            // Recalculate invoice if linked
            if ($request->sale_id) {
                $this->recalculateInvoiceStatus($request->sale_id);
            }

            $plainMessage = '₹' . number_format($request->amount, 2) . ' deducted from wallet! Remaining: ₹' . number_format($newBalance, 2);
            $htmlMessage  = '💰 ₹' . number_format($request->amount, 2) . ' used from wallet successfully!<br>Remaining wallet balance: ₹' . number_format($newBalance, 2);

            // Return JSON for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'     => true,
                    'message'     => '✅ ' . $plainMessage,
                    'new_balance' => $newBalance,
                ]);
            }

            return redirect()->back()->with('success', $htmlMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet Use Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace'   => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Error: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Error using wallet: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete wallet transaction with cascade
     */
    public function destroy($walletId)
    {
        DB::beginTransaction();

        try {
            $wallet = CustomerWallet::lockForUpdate()->findOrFail($walletId);
            $customer = Customer::lockForUpdate()->findOrFail($wallet->customer_id);

            // Analyze impact before deletion
            $impact = $this->analyzeDeleteImpact($walletId);
            $affectedInvoiceIds = [];

            // Get current wallet balance before deletion
            $currentWalletBalance = $this->getCurrentWalletBalance($customer->id);
            $newWalletBalance = $currentWalletBalance;

            // CASE 1: Credit entry (wallet add)
            if ($wallet->type == 'credit') {
                $result = $this->handleCreditDeletion($wallet, $customer);
                $affectedInvoiceIds = $result['affected_invoices'];
                $newWalletBalance = $result['new_balance'];
            }
            // CASE 2: Debit entry (wallet use)
            else if ($wallet->type == 'debit') {
                $result = $this->handleDebitDeletion($wallet, $customer);
                $affectedInvoiceIds = $result['affected_invoices'];
                $newWalletBalance = $result['new_balance'];
            }

            // Delete the wallet entry itself
            $wallet->delete();

            // Delete any remaining associated payments
            Payment::where('wallet_id', $walletId)->delete();

            // Recalculate wallet balance
            $this->recalculateWalletBalance($customer->id);

            // Recalculate all affected invoices
            foreach (array_unique($affectedInvoiceIds) as $invoiceId) {
                $this->recalculateInvoiceStatus($invoiceId);
            }

            DB::commit();

            $message = $this->formatDeleteMessage($wallet, $impact);

            return redirect()->back()->with('success', nl2br($message));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet Delete Error: ' . $e->getMessage(), [
                'wallet_id' => $walletId,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error deleting wallet transaction: ' . $e->getMessage());
        }
    }

    /**
     * Handle deletion of credit wallet entry
     */
    private function handleCreditDeletion($wallet, $customer)
    {
        $affectedInvoiceIds = [];
        $newWalletBalance = $this->getCurrentWalletBalance($customer->id);

        // Find all payments that used this wallet add
        $usages = Payment::where('source_wallet_id', $wallet->id)
            ->where('remarks', 'ADVANCE_USED')
            ->with('sale')
            ->get();

        foreach ($usages as $usage) {
            if ($usage->sale) {
                $affectedInvoiceIds[] = $usage->sale_id;

                // Delete the usage payment
                $usage->delete();

                // Recalculate invoice status AFTER deletion
                $this->recalculateInvoiceStatus($usage->sale_id);
            }
        }

        // Find if this wallet add was created from an invoice
        $sourcePayment = Payment::where('wallet_id', $wallet->id)
            ->where('remarks', 'WALLET_ADD')
            ->with('sale')
            ->first();

        if ($sourcePayment && $sourcePayment->sale_id) {
            $affectedInvoiceIds[] = $sourcePayment->sale_id;

            // Update source payment to remove wallet link
            $sourcePayment->wallet_id = null;
            $sourcePayment->save();

            // Recalculate source invoice
            $this->recalculateInvoiceStatus($sourcePayment->sale_id);
        }

        return [
            'affected_invoices' => $affectedInvoiceIds,
            'new_balance' => $newWalletBalance
        ];
    }

    /**
     * Handle deletion of debit wallet entry
     */
    private function handleDebitDeletion($wallet, $customer)
    {
        $affectedInvoiceIds = [];
        $currentBalance = $this->getCurrentWalletBalance($customer->id);
        $newWalletBalance = $currentBalance + $wallet->amount;

        $payment = Payment::where('wallet_id', $wallet->id)
            ->where('remarks', 'ADVANCE_USED')
            ->with('sale')
            ->first();

        if ($payment && $payment->sale) {
            $affectedInvoiceIds[] = $payment->sale_id;

            // Delete the payment
            $payment->delete();

            // Recalculate invoice AFTER deletion
            $this->recalculateInvoiceStatus($payment->sale_id);

            // Create a new wallet entry for the refund
            $refundWallet = new CustomerWallet();
            $refundWallet->customer_id = $customer->id;
            $refundWallet->type = 'credit';
            $refundWallet->amount = $wallet->amount;
            $refundWallet->balance = $newWalletBalance;
            $refundWallet->reference = 'Refund from deleted wallet transaction';
            $refundWallet->save();

            // Update customer wallet balance
            $customer->wallet_balance = $newWalletBalance;
            $customer->save();
        }

        return [
            'affected_invoices' => $affectedInvoiceIds,
            'new_balance' => $newWalletBalance
        ];
    }

    /**
     * Get delete impact preview
     */
    public function deleteImpact($walletId)
    {
        try {
            $wallet = CustomerWallet::with('customer')->findOrFail($walletId);

            $analysis = $this->analyzeDeleteImpact($walletId);

            return response()->json([
                'success' => true,
                'type' => $wallet->type,
                'type_label' => $wallet->type == 'credit' ? 'Wallet Add' : 'Wallet Use',
                'amount' => $wallet->amount,
                'customer_name' => $wallet->customer->name,
                'current_balance' => $this->getCurrentWalletBalance($wallet->customer_id),
                'linked_invoices' => $analysis['invoices_affected'],
                'has_links' => count($analysis['invoices_affected']) > 0,
                'warning' => $analysis['warning']
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Impact Error: ' . $e->getMessage(), [
                'wallet_id' => $walletId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error analyzing impact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet history with running balance
     */
    public function getHistory($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);

            $walletHistory = CustomerWallet::where('customer_id', $customerId)
                ->with(['payments.sale'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $payment = $item->payments->first();

                    return [
                        'id' => $item->id,
                        'date' => $item->created_at->format('Y-m-d H:i:s'),
                        'type' => $item->type,
                        'type_label' => $item->type == 'credit' ? '💰 Added' : '💳 Used',
                        'amount' => $item->amount,
                        'balance' => $item->balance,
                        'reference' => $item->reference,
                        'linked_invoice' => $payment && $payment->sale ? [
                            'id' => $payment->sale->id,
                            'invoice_no' => $payment->sale->invoice_no
                        ] : null
                    ];
                });

            $totals = [
                'added' => CustomerWallet::where('customer_id', $customerId)
                    ->where('type', 'credit')->sum('amount'),
                'used' => CustomerWallet::where('customer_id', $customerId)
                    ->where('type', 'debit')->sum('amount')
            ];

            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'mobile' => $customer->mobile
                ],
                'current_balance' => $this->getCurrentWalletBalance($customer->id),
                'totals' => $totals,
                'history' => $walletHistory,
                'total_transactions' => $walletHistory->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Get History Error: ' . $e->getMessage(), [
                'customer_id' => $customerId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Wallet report
     */
    public function report(Request $request)
    {
        $query = Customer::where(function($q) {
            $q->where('wallet_balance', '!=', 0)
              ->orWhereHas('wallet');
        });

        // Filter by balance type
        if ($request->type == 'positive') {
            $query->where('wallet_balance', '>', 0);
        } elseif ($request->type == 'zero') {
            $query->where('wallet_balance', 0);
        }

        $customers = $query->withCount('wallet')
            ->orderBy('wallet_balance', 'desc')
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total_balance' => Customer::sum('wallet_balance'),
            'total_customers' => Customer::where('wallet_balance', '>', 0)->count(),
            'total_credits' => CustomerWallet::where('type', 'credit')->sum('amount'),
            'total_debits' => CustomerWallet::where('type', 'debit')->sum('amount'),
            'avg_balance' => Customer::where('wallet_balance', '>', 0)->avg('wallet_balance') ?? 0
        ];

        $recentTransactions = CustomerWallet::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('customers.wallet-report', compact(
            'customers',
            'summary',
            'recentTransactions',
            'request'
        ));
    }

    /**
     * Recalculate wallet balance for a customer
     */
    public function recalculate($customerId)
    {
        DB::beginTransaction();

        try {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);

            // Calculate correct balance from transactions
            $correctBalance = $this->calculateCorrectBalance($customerId);

            // Update customer record
            $customer->wallet_balance = $correctBalance;
            $customer->save();

            // Verify all invoices are in sync
            $this->recalculateAllCustomerInvoices($customerId);

            DB::commit();

            return redirect()->back()->with(
                'success',
                '✅ Wallet balance recalculated successfully!<br>' .
                'New balance: ₹' . number_format($correctBalance, 2) . '<br>' .
                'All invoices have been verified.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet Recalculate Error: ' . $e->getMessage(), [
                'customer_id' => $customerId,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error recalculating wallet: ' . $e->getMessage());
        }
    }

    /**
     * ==================== PRIVATE HELPER METHODS ====================
     */

    /**
     * Get current wallet balance from wallet transactions
     */
    private function getCurrentWalletBalance($customerId)
    {
        $lastWallet = CustomerWallet::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastWallet ? (float) $lastWallet->balance : 0.00;
    }

    /**
     * Calculate correct balance from all transactions
     */
    private function calculateCorrectBalance($customerId)
    {
        $credits = CustomerWallet::where('customer_id', $customerId)
            ->where('type', 'credit')
            ->sum('amount');

        $debits = CustomerWallet::where('customer_id', $customerId)
            ->where('type', 'debit')
            ->sum('amount');

        return $credits - $debits;
    }

    /**
     * Apply FIFO (First In First Out) logic to wallet usage
     */
    private function applyFIFO($customerId, $amount)
    {
        $credits = CustomerWallet::where('customer_id', $customerId)
            ->where('type', 'credit')
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingAmount = $amount;

        foreach ($credits as $credit) {
            $usedAmount = Payment::where('source_wallet_id', $credit->id)
                ->where('remarks', 'ADVANCE_USED')
                ->sum('amount');

            $available = $credit->amount - $usedAmount;

            if ($available > 0) {
                // This credit has some available balance
                if ($remainingAmount <= $available) {
                    // This credit can cover the entire remaining amount
                    return $credit->id;
                } else {
                    // This credit can only cover part, move to next
                    $remainingAmount -= $available;
                }
            }
        }

        return null;
    }

    /**
     * Recalculate invoice payment status
     */
    private function recalculateInvoiceStatus($saleId)
    {
        $sale = Sale::find($saleId);
        if (!$sale) {
            Log::warning("Sale not found for ID: {$saleId}");
            return;
        }

        // Get all paid payments for this invoice
        $payments = Payment::where('sale_id', $saleId)
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->get();

        $totalPaid = $payments->sum('amount');
        $remaining = $sale->grand_total - $totalPaid;

        Log::info("Recalculating Invoice #{$sale->invoice_no}", [
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'grand_total' => $sale->grand_total
        ]);

        // Determine new status
        if ($remaining <= 0.01) {
            $newStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $newStatus = 'partial';
        } else {
            $newStatus = 'unpaid';
        }

        $sale->payment_status = $newStatus;
        $sale->paid_amount = $totalPaid;
        $sale->save();

        Log::info("Invoice #{$sale->invoice_no} status updated to {$newStatus}");
    }

    /**
     * Recalculate wallet balance from transactions
     */
    private function recalculateWalletBalance($customerId)
    {
        $lastWallet = CustomerWallet::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        $correctBalance = $lastWallet ? $lastWallet->balance : 0;

        Customer::where('id', $customerId)->update([
            'wallet_balance' => $correctBalance
        ]);

        Log::info("Wallet balance recalculated for customer #{$customerId}: ₹{$correctBalance}");
    }

    /**
     * Recalculate all invoices for a customer
     */
    private function recalculateAllCustomerInvoices($customerId)
    {
        $invoices = Sale::where('customer_id', $customerId)->get();

        foreach ($invoices as $invoice) {
            $this->recalculateInvoiceStatus($invoice->id);
        }

        Log::info("All invoices recalculated for customer #{$customerId}");
    }

    /**
     * Analyze impact of deleting a wallet transaction
     */
    private function analyzeDeleteImpact($walletId)
    {
        $wallet = CustomerWallet::find($walletId);
        $invoicesAffected = [];
        $warning = '';

        if ($wallet->type == 'credit') {
            // Find usages of this wallet add
            $usages = Payment::where('source_wallet_id', $walletId)
                ->where('remarks', 'ADVANCE_USED')
                ->with('sale')
                ->get();

            foreach ($usages as $usage) {
                if ($usage->sale) {
                    $invoicesAffected[] = [
                        'id' => $usage->sale->id,
                        'invoice_no' => $usage->sale->invoice_no,
                        'amount' => $usage->amount,
                        'type' => 'used_in'
                    ];
                }
            }

            // Find source invoice
            $source = Payment::where('wallet_id', $walletId)
                ->where('remarks', 'WALLET_ADD')
                ->with('sale')
                ->first();

            if ($source && $source->sale) {
                $invoicesAffected[] = [
                    'id' => $source->sale->id,
                    'invoice_no' => $source->sale->invoice_no,
                    'amount' => $source->amount,
                    'type' => 'source'
                ];
            }

            if (count($invoicesAffected) > 0) {
                $invoiceList = collect($invoicesAffected)->pluck('invoice_no')->unique()->join(', ');
                $warning = "⚠️ This will affect invoice(s): {$invoiceList}";
            }
        } else {
            // Debit entry
            $usage = Payment::where('wallet_id', $walletId)
                ->where('remarks', 'ADVANCE_USED')
                ->with('sale')
                ->first();

            if ($usage && $usage->sale) {
                $invoicesAffected[] = [
                    'id' => $usage->sale->id,
                    'invoice_no' => $usage->sale->invoice_no,
                    'amount' => $usage->amount,
                    'type' => 'used_in'
                ];
                $warning = "⚠️ This will affect invoice #{$usage->sale->invoice_no}";
            }
        }

        return [
            'invoices_affected' => $invoicesAffected,
            'warning' => $warning
        ];
    }

    /**
     * Format delete message
     */
    private function formatDeleteMessage($wallet, $impact)
    {
        $type = $wallet->type == 'credit' ? 'Wallet Add' : 'Wallet Use';

        $message = "✅ Wallet transaction deleted successfully!\n\n";
        $message .= "Type: {$type}\n";
        $message .= "Amount: ₹" . number_format($wallet->amount, 2) . "\n";

        if (!empty($impact['invoices_affected'])) {
            $uniqueInvoices = collect($impact['invoices_affected'])
                ->pluck('invoice_no')
                ->unique()
                ->map(function($inv) { return "#{$inv}"; })
                ->join(', ');

            $message .= "\n📄 Affected Invoices: {$uniqueInvoices}\n";
            $message .= "\n✅ These invoices have been updated.";
        } else {
            $message .= "\n✅ No linked invoices affected.";
        }

        if ($wallet->type == 'debit') {
            $message .= "\n💰 Amount refunded back to wallet.";
        }

        return $message;
    }
}
