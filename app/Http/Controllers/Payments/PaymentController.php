<?php
// app/Http/Controllers/Payments/PaymentController.php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\CustomerWallet;
use App\Models\EmiPlan;

class PaymentController extends Controller
{
    /**
     * Show payment form
     */
    public function create($saleId)
    {
        $sale = Sale::with('customer', 'payments')->findOrFail($saleId);

        // If already paid
        if ($sale->payment_status === 'paid') {
            return redirect()->route('sales.show', $sale->id)
                ->with('error', 'Invoice already fully paid.');
        }

        // If EMI is running
        if ($sale->payment_status === 'emi') {
            return redirect()->route('sales.show', $sale->id)
                ->with('error', 'EMI is running. Please pay EMI only.');
        }

        // Calculate paid amount for this invoice only
        $paidAmount = Payment::where('sale_id', $sale->id)
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');

        $remaining = $sale->grand_total - $paidAmount;

        // Get customer wallet and balance details
        $walletBalance = 0;
        $dueBalance = 0;
        $openBalance = 0;

        if ($sale->customer) {
            $customer = $sale->customer;

            // Get current wallet balance from wallet transactions
            $latestWallet = CustomerWallet::where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $walletBalance = $latestWallet ? $latestWallet->balance : 0;

            // open_balance > 0 means customer owes us (due from previous invoices)
            // open_balance < 0 means customer has advance with us
            $openBalance = $customer->open_balance ?? 0;

            if ($customer->open_balance > 0) {
                $dueBalance = $customer->open_balance;
            }
        }

        return view('payments.create', compact(
            'sale',
            'remaining',
            'walletBalance',
            'dueBalance',
            'openBalance',
            'paidAmount'
        ));
    }

    /**
     * Delete all payments for a customer
     */
    public function destroyAll($customerId)
    {
        DB::beginTransaction();

        try {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);

            $allPayments = Payment::where('customer_id', $customerId)->get();
            $totalAmount = $allPayments->sum('amount');

            foreach ($allPayments as $payment) {
                // Delete associated wallet transactions
                if ($payment->wallet_id) {
                    CustomerWallet::where('id', $payment->wallet_id)->delete();
                }
                $payment->delete();
            }

            // Delete all wallet entries
            CustomerWallet::where('customer_id', $customerId)->delete();

            // Reset customer balances
            $customer->wallet_balance = 0;

            $invoices = Sale::where('customer_id', $customerId)->get();
            $totalInvoiceAmount = $invoices->sum('grand_total');

            $customer->open_balance = $totalInvoiceAmount;
            $customer->save();

            // Reset all invoices to unpaid
            foreach ($invoices as $invoice) {
                $invoice->payment_status = 'unpaid';
                $invoice->paid_amount = 0;
                $invoice->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ All payments deleted! ₹' . number_format($totalAmount, 2) . ' removed.',
                'data' => [
                    'total_amount' => $totalAmount,
                    'new_open_balance' => $totalInvoiceAmount
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete All Payments Error: ' . $e->getMessage(), [
                'customer_id' => $customerId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store payment
     */
    public function store(Request $request)
    {
        Log::info('===== PAYMENT STORE STARTED =====');
        Log::info('Request data:', $request->all());

        try {
            // Validation with proper rules
            $validated = $this->validatePaymentRequest($request);

            DB::beginTransaction();
            Log::info('Transaction started');

            // Find Sale and Customer with lock
            $sale = Sale::lockForUpdate()->findOrFail($request->sale_id);
            $customer = Customer::lockForUpdate()->findOrFail($sale->customer_id);

            Log::info("Processing payment for: Invoice #{$sale->invoice_no}, Customer: {$customer->name}");

            // Get current wallet balance
            $currentWalletBalance = $this->getCurrentWalletBalance($customer->id);
            Log::info("Current wallet balance: {$currentWalletBalance}");

            // Process based on payment type
            if ($request->is_advance_only == '1') {
                $this->processPureAdvance($request, $customer, $currentWalletBalance);
            } elseif ($request->method == 'emi') {
                $this->processEmiPayment($request, $sale, $customer);
            } else {
                $this->processInvoicePayment($request, $sale, $customer, $currentWalletBalance);
            }

            DB::commit();
            Log::info('✅ Transaction committed successfully');

            return redirect()->route('sales.show', $sale->id)
                ->with('success', $this->getSuccessMessage($request, $sale));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed:', $e->errors());
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Validate payment request
     */
    private function validatePaymentRequest(Request $request)
    {
        $rules = [
            'sale_id' => 'required|exists:sales,id',
            'payment_type' => 'required|in:full,partial,excess',
            'method' => 'required|in:cash,upi,card,net_banking,emi,wallet',
            'payment_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'advance_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'wallet_used' => 'nullable|numeric|min:0|max:9999999.99',
            'down_payment' => 'nullable|numeric|min:0|max:9999999.99',
            'emi_months' => 'nullable|integer|min:1|max:60',
            'emi_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'transaction_id' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'is_advance_only' => 'nullable|in:0,1',
        ];

        // Conditional validation
        if ($request->is_advance_only == '1') {
            $rules['advance_amount'] = 'required|numeric|min:1';
        } elseif ($request->method == 'emi') {
            $rules['down_payment'] = 'required|numeric|min:1';
            $rules['emi_months'] = 'required|integer|min:1';
            $rules['emi_amount'] = 'required|numeric|min:1';
        } else {
            $rules['payment_amount'] = 'required_without:wallet_used|numeric|min:0';
            $rules['wallet_used'] = 'required_without:payment_amount|numeric|min:0';
        }

        return $request->validate($rules);
    }

    /**
     * Process pure advance payment - Add to Wallet
     */
    private function processPureAdvance($request, $customer, $currentWalletBalance)
    {
        $amount = $request->advance_amount ?? 0;

        if ($amount <= 0) {
            throw new \Exception('Please enter a valid advance amount.');
        }

        $newBalance = $currentWalletBalance + $amount;

        // Create wallet entry (credit)
        $wallet = CustomerWallet::create([
            'customer_id' => $customer->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance' => $newBalance,
            'reference' => $request->remarks ?? 'Pure advance payment'
        ]);

        // Create payment record linked to wallet
        Payment::create([
            'customer_id' => $customer->id,
            'sale_id' => null,
            'amount' => $amount,
            'method' => $request->method,
            'status' => 'paid',
            'remarks' => 'ADVANCE_ONLY',
            'transaction_id' => $request->transaction_id,
            'wallet_id' => $wallet->id
        ]);

        // Update customer balances
        $customer->wallet_balance = $newBalance;
        $customer->open_balance -= $amount; // Negative = advance
        $customer->save();

        Log::info("Pure advance added: ₹{$amount}, New wallet balance: {$newBalance}");
    }

    /**
     * Validate wallet usage before processing
     */
  private function validateWalletUsage($customerId, $amount)
{
    Log::info("========== 🔍 VALIDATE WALLET USAGE STARTED ==========");
    Log::info("Customer ID: {$customerId}, Amount: ₹{$amount}");

    // 🔥 FIX 1: Negative amount check
    if ($amount < 0) {
        Log::error("❌ Negative amount not allowed: ₹{$amount}");
        throw new \Exception('Wallet amount cannot be negative');
    }

    if ($amount == 0) {
        Log::info("ℹ️ Amount is zero, skipping validation");
        return [
            'is_valid' => true,
            'total_available' => 0,
            'requested_amount' => 0,
            'shortfall' => 0,
            'credit_details' => []
        ];
    }

    // 🔥 FIX 2: Better query - with locking and subquery
    $credits = CustomerWallet::where('customer_id', $customerId)
        ->where('type', 'credit')
        ->lockForUpdate()  // ✅ Add lock to prevent race conditions
        ->orderBy('created_at', 'asc')
        ->get();

    if ($credits->isEmpty()) {
        Log::warning("⚠️ No credit wallets found for customer {$customerId}");
        return [
            'is_valid' => false,
            'total_available' => 0,
            'requested_amount' => $amount,
            'shortfall' => $amount,
            'credit_details' => []
        ];
    }

    $totalAvailable = 0;
    $creditDetails = [];

    // 🔥 FIX 3: Get all used amounts in single query (performance improvement)
    $creditIds = $credits->pluck('id')->toArray();
    $usedAmounts = Payment::whereIn('source_wallet_id', $creditIds)
        ->where('remarks', 'ADVANCE_USED')
        ->selectRaw('source_wallet_id, SUM(amount) as total_used')
        ->groupBy('source_wallet_id')
        ->pluck('total_used', 'source_wallet_id')
        ->toArray();

    foreach ($credits as $credit) {
        $usedAmount = $usedAmounts[$credit->id] ?? 0;
        $available = $credit->amount - $usedAmount;

        // Available should never be negative
        $available = max(0, $available);

        $totalAvailable += $available;

        $creditDetails[] = [
            'id' => $credit->id,
            'date' => $credit->created_at->format('Y-m-d H:i:s'),
            'total' => (float)$credit->amount,
            'used' => (float)$usedAmount,
            'available' => (float)$available
        ];

        Log::info("Credit #{$credit->id}:");
        Log::info("  • Date: {$credit->created_at->format('Y-m-d')}");
        Log::info("  • Total: ₹{$credit->amount}");
        Log::info("  • Used: ₹{$usedAmount}");
        Log::info("  • Available: ₹{$available}");
    }

    $isValid = $totalAvailable >= $amount;
    $shortfall = $isValid ? 0 : $amount - $totalAvailable;

    Log::info("-----------------------------------");
    Log::info("SUMMARY:");
    Log::info("  • Total credits found: " . count($credits));
    Log::info("  • Total available: ₹{$totalAvailable}");
    Log::info("  • Requested amount: ₹{$amount}");
    Log::info("  • Shortfall: ₹{$shortfall}");
    Log::info("  • Status: " . ($isValid ? "✅ SUFFICIENT" : "❌ INSUFFICIENT"));
    Log::info("========== 🔍 VALIDATE WALLET USAGE ENDED ==========");

    return [
        'is_valid' => $isValid,
        'total_available' => $totalAvailable,
        'requested_amount' => $amount,
        'shortfall' => $shortfall,
        'credit_details' => $creditDetails,
        'total_credits' => count($credits)
    ];
}
    /**
     * Process invoice payment with wallet usage
     */
  private function processInvoicePayment($request, $sale, $customer, $currentWalletBalance)
{
    Log::info("========== 💳 PROCESS INVOICE PAYMENT STARTED ==========");
    Log::info("Invoice #{$sale->invoice_no} | Customer: {$customer->name} (ID: {$customer->id})");

    $cashAmount = (float)($request->payment_amount ?? 0);
    $walletUsed = (float)($request->wallet_used ?? 0);
    $totalPayment = $cashAmount + $walletUsed;

    Log::info("Payment details:");
    Log::info("  • Cash amount: ₹{$cashAmount}");
    Log::info("  • Wallet used: ₹{$walletUsed}");
    Log::info("  • Total payment: ₹{$totalPayment}");
    Log::info("  • Current wallet balance: ₹{$currentWalletBalance}");

    if ($totalPayment <= 0) {
        Log::error("❌ No payment amount provided");
        throw new \Exception('Please enter a payment amount or use wallet.');
    }

    // Get total paid for this invoice so far
    $paidSoFar = Payment::where('sale_id', $sale->id)
        ->where('status', 'paid')
        ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
        ->sum('amount');

    $remainingDue = $sale->grand_total - $paidSoFar;
    Log::info("Invoice status:");
    Log::info("  • Grand total: ₹{$sale->grand_total}");
    Log::info("  • Paid so far: ₹{$paidSoFar}");
    Log::info("  • Remaining due: ₹{$remainingDue}");

    $newWalletBalance = $currentWalletBalance;
    $affectedCreditIds = [];

    // ========== 1. PROCESS WALLET USAGE ==========
    if ($walletUsed > 0) {
        Log::info("🔄 Processing wallet usage: ₹{$walletUsed}");

        // ✅ Enhanced validation
        $validation = $this->validateWalletUsage($customer->id, $walletUsed);

        if (!$validation['is_valid']) {
            Log::error("❌ Insufficient wallet balance. Need ₹{$walletUsed}, but only ₹{$validation['total_available']} available");
            throw new \Exception('Insufficient wallet balance. Available: ₹' . number_format($validation['total_available'], 2));
        }

        // Get source wallet ID using FIFO
        $sourceWalletId = $this->applyFIFO($customer->id, $walletUsed);

        if (!$sourceWalletId) {
            Log::error("❌ FIFO failed despite sufficient balance!");
            Log::error("Validation details: " . json_encode($validation));
            throw new \Exception('System error: Could not determine source wallet. Please contact support.');
        }

        Log::info("✅ Source wallet ID: {$sourceWalletId}");

        // Create wallet entry (debit)
        $newWalletBalance -= $walletUsed;
        $wallet = CustomerWallet::create([
            'customer_id' => $customer->id,
            'type' => 'debit',
            'amount' => $walletUsed,
            'balance' => $newWalletBalance,
            'reference' => 'Used for invoice #' . $sale->invoice_no
        ]);

        Log::info("✅ Created debit wallet entry ID: {$wallet->id}");

        // Record wallet usage with source_wallet_id
        $payment = Payment::create([
            'sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'amount' => $walletUsed,
            'method' => 'wallet',
            'status' => 'paid',
            'remarks' => 'ADVANCE_USED',
            'transaction_id' => $request->transaction_id,
            'wallet_id' => $wallet->id,
            'source_wallet_id' => $sourceWalletId
        ]);

        Log::info("✅ Created ADVANCE_USED payment ID: {$payment->id} with source_wallet_id: {$sourceWalletId}");

        // 🔥 FIXED: Wallet use se customer ka DUE kam hoga, isliye open_balance GHATANA hai
        $customer->open_balance -= $walletUsed;
        Log::info("Updated open_balance: -₹{$walletUsed} = ₹{$customer->open_balance}");

        $affectedCreditIds[] = $sourceWalletId;
    }

    // ========== 2. PROCESS CASH PAYMENT ==========
    if ($cashAmount > 0) {
        Log::info("💰 Processing cash payment: ₹{$cashAmount}");

        $newPaidSoFar = $paidSoFar + $walletUsed;
        $remainingAfterWallet = $sale->grand_total - $newPaidSoFar;

        $invoicePortion = min($cashAmount, max(0, $remainingAfterWallet));
        $excess = $cashAmount - $invoicePortion;

        Log::info("  • Invoice portion: ₹{$invoicePortion}");
        Log::info("  • Excess to wallet: ₹{$excess}");

        // Record invoice payment portion
        if ($invoicePortion > 0) {
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'customer_id' => $customer->id,
                'amount' => $invoicePortion,
                'method' => $request->method,
                'status' => 'paid',
                'remarks' => 'INVOICE',
                'transaction_id' => $request->transaction_id
            ]);

            Log::info("✅ Created INVOICE payment ID: {$payment->id} for ₹{$invoicePortion}");

            // 🔥 FIXED: Invoice payment se bhi customer ka DUE kam hoga
            $customer->open_balance -= $invoicePortion;
            Log::info("Updated open_balance: -₹{$invoicePortion} = ₹{$customer->open_balance}");
        }

        // Record excess that goes to wallet
        if ($excess > 0) {
            $newWalletBalance += $excess;

            $wallet = CustomerWallet::create([
                'customer_id' => $customer->id,
                'type' => 'credit',
                'amount' => $excess,
                'balance' => $newWalletBalance,
                'reference' => 'Excess from invoice #' . $sale->invoice_no
            ]);

            Log::info("✅ Created credit wallet entry ID: {$wallet->id} for ₹{$excess}");

            $payment = Payment::create([
                'sale_id' => $sale->id,
                'customer_id' => $customer->id,
                'amount' => $excess,
                'method' => $request->method,
                'status' => 'paid',
                'remarks' => 'EXCESS_TO_ADVANCE',
                'transaction_id' => $request->transaction_id,
                'wallet_id' => $wallet->id
            ]);

            Log::info("✅ Created EXCESS_TO_ADVANCE payment ID: {$payment->id}");

            // 🔥 Excess payment se customer ka advance badhega (negative balance)
            $customer->open_balance -= $excess;  // Ye already sahi tha
            Log::info("Updated open_balance: -₹{$excess} = ₹{$customer->open_balance}");
        }
    }

    // Update customer wallet balance and save
    $customer->wallet_balance = $newWalletBalance;
    $customer->save();

    Log::info("Final wallet balance: ₹{$newWalletBalance}");

    // ========== 3. RECALCULATE INVOICE STATUS ==========
    $this->recalculateInvoiceStatus($sale->id);
    $sale->refresh();

    Log::info("Invoice #{$sale->invoice_no} new status: {$sale->payment_status}");

    // Log affected credits for audit trail
    if (!empty($affectedCreditIds)) {
        Log::info("Affected credit wallets: " . implode(', ', $affectedCreditIds));
    }

    Log::info("========== 💳 PROCESS INVOICE PAYMENT COMPLETED ==========");
}

    /**
     * Process EMI payment
     */
  private function processEmiPayment($request, $sale, $customer)
{
    Log::info("========== 📅 PROCESS EMI PAYMENT STARTED ==========");
    Log::info("Invoice #{$sale->invoice_no} | Customer: {$customer->name} (ID: {$customer->id})");

    $downPayment = (float)($request->down_payment ?? 0);
    $emiMonths = (int)($request->emi_months ?? 0);
    $emiAmount = (float)($request->emi_amount ?? 0);
    $paymentMethod = $request->method ?? 'cash';

    Log::info("EMI Details:");
    Log::info("  • Down Payment: ₹{$downPayment}");
    Log::info("  • EMI Months: {$emiMonths}");
    Log::info("  • Monthly EMI: ₹{$emiAmount}");
    Log::info("  • Payment Method: {$paymentMethod}");

    // ========== VALIDATIONS ==========
    if ($downPayment <= 0) {
        throw new \Exception('Please enter down payment amount.');
    }

    if ($emiMonths <= 0) {
        throw new \Exception('Please select EMI months.');
    }

    if ($emiAmount <= 0) {
        throw new \Exception('EMI amount calculation failed.');
    }

    // Get paid amount so far
    $paidSoFar = Payment::where('sale_id', $sale->id)
        ->where('status', 'paid')
        ->whereIn('remarks', ['INVOICE', 'EMI_DOWN'])
        ->sum('amount');

    $remainingDue = $sale->grand_total - $paidSoFar;

    if ($downPayment >= $remainingDue) {
        throw new \Exception('Down payment cannot be equal to or greater than remaining amount. Use regular payment instead.');
    }

    // ========== GET CURRENT BALANCES ==========
    $currentWalletBalance = $this->getCurrentWalletBalance($customer->id);
    $newWalletBalance = $currentWalletBalance;
    $walletId = null;
    $sourceWalletId = null;

    // ========== 🔥 FIX 1: HANDLE WALLET PAYMENT ==========
    if ($paymentMethod === 'wallet') {
        Log::info("🔄 Processing wallet payment for down payment: ₹{$downPayment}");

        // Check wallet balance
        if ($currentWalletBalance < $downPayment) {
            throw new \Exception('Insufficient wallet balance. Available: ₹' . number_format($currentWalletBalance, 2));
        }

        // 🔥 FIX 2: Validate wallet usage with FIFO
        $validation = $this->validateWalletUsage($customer->id, $downPayment);

        if (!$validation['is_valid']) {
            throw new \Exception('Insufficient wallet balance. Available: ₹' . number_format($validation['total_available'], 2));
        }

        // 🔥 FIX 3: Get source wallet ID using FIFO
        $sourceWalletId = $this->applyFIFO($customer->id, $downPayment);

        if (!$sourceWalletId) {
            throw new \Exception('System error: Could not determine source wallet');
        }

        // Create wallet entry (debit)
        $newWalletBalance = $currentWalletBalance - $downPayment;
        $wallet = CustomerWallet::create([
            'customer_id' => $customer->id,
            'type' => 'debit',
            'amount' => $downPayment,
            'balance' => $newWalletBalance,
            'reference' => 'EMI down payment for invoice #' . $sale->invoice_no
        ]);

        $walletId = $wallet->id;

        Log::info("✅ Created debit wallet entry ID: {$walletId}");
        Log::info("✅ Source wallet ID: {$sourceWalletId}");
    }

    // ========== 🔥 FIX 4: RECORD DOWN PAYMENT ==========
    $payment = Payment::create([
        'sale_id' => $sale->id,
        'customer_id' => $customer->id,
        'amount' => $downPayment,
        'method' => $paymentMethod,
        'status' => 'paid',
        'remarks' => 'EMI_DOWN',
        'transaction_id' => $request->transaction_id,
        'wallet_id' => $walletId,
        'source_wallet_id' => $sourceWalletId
    ]);

    Log::info("✅ Created EMI_DOWN payment ID: {$payment->id} for ₹{$downPayment}");

    // ========== 🔥 FIX 5: UPDATE OPEN BALANCE ==========
    // Down payment se customer ka due kam hoga
    $customer->open_balance -= $downPayment;
    Log::info("Updated open_balance: -₹{$downPayment} = ₹{$customer->open_balance}");

    // ========== 🔥 FIX 6: UPDATE WALLET BALANCE ==========
    if ($paymentMethod === 'wallet') {
        $customer->wallet_balance = $newWalletBalance;
        Log::info("Updated wallet balance: ₹{$newWalletBalance}");
    }

    $customer->save();

    // ========== CREATE EMI PLAN ==========
    $emiPlan = EmiPlan::create([
        'sale_id' => $sale->id,
        'total_amount' => $remainingDue,
        'down_payment' => $downPayment,
        'months' => $emiMonths,
        'emi_amount' => $emiAmount,
        'status' => 'running',
    ]);

    Log::info("✅ Created EMI plan ID: {$emiPlan->id}");
    Log::info("  • Total amount: ₹{$remainingDue}");
    Log::info("  • Down payment: ₹{$downPayment}");
    Log::info("  • EMI: ₹{$emiAmount} x {$emiMonths} months");

    // ========== UPDATE SALE STATUS ==========
    $sale->payment_status = 'emi';
    $sale->save();

    Log::info("✅ Sale #{$sale->id} status updated to 'emi'");
    Log::info("========== 📅 PROCESS EMI PAYMENT COMPLETED ==========");
}

    /**
     * Mark invoice as due (add to customer balance)
     */
    public function markAsDue(Sale $sale)
    {
        DB::beginTransaction();

        try {
            // Calculate paid amount
            $paidAmount = Payment::where('sale_id', $sale->id)
                ->where('status', 'paid')
                ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
                ->sum('amount');

            $remaining = $sale->grand_total - $paidAmount;

            if ($remaining <= 0) {
                return back()->with('error', 'Invoice is already fully paid.');
            }

            // Check if already marked as due
            $alreadyDue = Payment::where('sale_id', $sale->id)
                ->where('remarks', 'INVOICE_DUE')
                ->exists();

            if ($alreadyDue) {
                return back()->with('error', 'Invoice already marked as due.');
            }

            // Create due record
            Payment::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'amount' => $remaining,
                'method' => 'due',
                'status' => 'pending',
                'remarks' => 'INVOICE_DUE',
            ]);

            // Update customer balance (positive = they owe us)
            $customer = $sale->customer;
            $customer->open_balance += $remaining;
            $customer->save();

            DB::commit();

            return back()->with('success', 'Invoice marked as due. ₹' . number_format($remaining, 2) . ' added to customer balance.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mark as Due Error: ' . $e->getMessage(), [
                'sale_id' => $sale->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error marking invoice as due: ' . $e->getMessage());
        }
    }

    /**
     * Delete bulk payments for an invoice - AJAX version
     */
    public function deleteBulk($saleId)
    {
        DB::beginTransaction();

        try {
            $currentSale = Sale::lockForUpdate()->findOrFail($saleId);
            $customer = Customer::lockForUpdate()->findOrFail($currentSale->customer_id);

            // Get all payments for current sale
            $currentPayments = Payment::where('sale_id', $saleId)->get();

            if ($currentPayments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No transactions found for this invoice.'
                ], 404);
            }

            $transactionCount = $currentPayments->count();
            $totalAmount = $currentPayments->sum('amount');

            // Calculate wallet impact
            $walletCreditsFromThisInvoice = $currentPayments
                ->where('status', 'paid')
                ->whereIn('remarks', ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])
                ->sum('amount');

            $walletDebitsToThisInvoice = $currentPayments
                ->where('status', 'paid')
                ->where('remarks', 'ADVANCE_USED')
                ->sum('amount');

            // Delete wallet transactions linked to these payments
            foreach ($currentPayments as $payment) {
                if ($payment->wallet_id) {
                    CustomerWallet::where('id', $payment->wallet_id)->delete();
                }
            }

            // Update customer open_balance
            if ($walletCreditsFromThisInvoice > 0) {
                $customer->open_balance += $walletCreditsFromThisInvoice;
            }

            if ($walletDebitsToThisInvoice > 0) {
                $customer->open_balance -= $walletDebitsToThisInvoice;
            }

            // Delete all payments
            foreach ($currentPayments as $payment) {
                $payment->delete();
            }

            $customer->save();

            // Update current invoice status to unpaid
            $currentSale->payment_status = 'unpaid';
            $currentSale->paid_amount = 0;
            $currentSale->save();

            // Delete EMI plan if exists
            if ($currentSale->emiPlan) {
                $currentSale->emiPlan->delete();
            }

            // Recalculate wallet balance
            $this->recalculateWalletBalance($customer->id);

            // Recalculate all invoices for this customer
            $this->recalculateAllCustomerInvoices($customer->id);

            DB::commit();

            // Get updated wallet balance
            $newWalletBalance = $this->getCurrentWalletBalance($customer->id);

            return response()->json([
                'success' => true,
                'message' => '✅ All payments deleted successfully!',
                'data' => [
                    'invoice_id' => $currentSale->id,
                    'invoice_no' => $currentSale->invoice_no,
                    'transaction_count' => $transactionCount,
                    'total_amount' => $totalAmount,
                    'wallet_credits_removed' => $walletCreditsFromThisInvoice,
                    'wallet_debits_removed' => $walletDebitsToThisInvoice,
                    'new_wallet_balance' => $newWalletBalance,
                    'new_open_balance' => $customer->open_balance,
                    'invoice_status' => $currentSale->payment_status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Bulk Payments Error: ' . $e->getMessage(), [
                'sale_id' => $saleId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete single payment
     */
    public function destroy(Payment $payment)
    {
        DB::beginTransaction();

        try {
            $customer = Customer::lockForUpdate()->findOrFail($payment->customer_id);
            $saleId = $payment->sale_id;
            $remarks = $payment->remarks;
            $amount = $payment->amount;

            // Delete associated wallet transaction if exists
            if ($payment->wallet_id) {
                CustomerWallet::where('id', $payment->wallet_id)->delete();
            }

            // Reverse the effect based on payment type
            switch ($remarks) {
                case 'ADVANCE_ONLY':
                case 'EXCESS_TO_ADVANCE':
                case 'WALLET_ADD':
                    // Customer gave advance, deleting it reduces advance balance
                    $customer->open_balance += $amount;
                    break;

                case 'ADVANCE_USED':
                    // Customer used wallet for invoice
                    $customer->open_balance -= $amount;
                    break;

                case 'INVOICE_DUE':
                    // Invoice was marked as due
                    $customer->open_balance -= $amount;
                    break;

                case 'INVOICE':
                case 'EMI_DOWN':
                    // Direct invoice payment - no effect on customer balance
                    break;
            }

            $customer->save();

            // Delete the payment
            $payment->delete();

            // Recalculate wallet balance
            $this->recalculateWalletBalance($customer->id);

            // Recalculate affected invoice
            if ($saleId) {
                $this->recalculateInvoiceStatus($saleId);
            }

            // Recalculate all invoices for consistency
            $this->recalculateAllCustomerInvoices($customer->id);

            DB::commit();

            // Get updated wallet balance
            $newWalletBalance = $this->getCurrentWalletBalance($customer->id);

            return response()->json([
                'success' => true,
                'message' => '✅ Transaction deleted successfully!',
                'data' => [
                    'type' => str_replace('_', ' ', $remarks),
                    'amount' => $amount,
                    'wallet_balance' => $newWalletBalance,
                    'open_balance' => $customer->open_balance
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Delete Error: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply FIFO (First In First Out) logic to find source wallet
     */
   private function applyFIFO($customerId, $amount)
{
    Log::info("========== 🔍 FIFO STARTED ==========");
    Log::info("Customer ID: {$customerId}, Amount needed: ₹{$amount}");

    if ($amount <= 0) {
        Log::warning("⚠️ Amount is zero or negative, skipping FIFO");
        return null;
    }

    // 🔥 FIX 1: Add LOCK for race condition
    $credits = CustomerWallet::where('customer_id', $customerId)
        ->where('type', 'credit')
        ->lockForUpdate()  // ✅ IMPORTANT: Prevent double usage
        ->orderBy('created_at', 'asc')
        ->get();

    $creditCount = $credits->count();
    Log::info("Found {$creditCount} credit wallet(s)");

    if ($credits->isEmpty()) {
        Log::warning("❌ No credit wallets found for customer {$customerId}");
        Log::info("========== 🔍 FIFO ENDED (NO CREDITS) ==========");
        return null;
    }

    // 🔥 FIX 2: Get all used amounts in ONE query (performance)
    $creditIds = $credits->pluck('id')->toArray();
    $usedAmounts = Payment::whereIn('source_wallet_id', $creditIds)
        ->where('remarks', 'ADVANCE_USED')
        ->selectRaw('source_wallet_id, SUM(amount) as total_used')
        ->groupBy('source_wallet_id')
        ->pluck('total_used', 'source_wallet_id')
        ->toArray();

    $remainingAmount = $amount;
    $totalAvailable = 0;
    $usedCredits = []; // Track which credits we're using

    foreach ($credits as $index => $credit) {
        $usedAmount = $usedAmounts[$credit->id] ?? 0;
        $available = $credit->amount - $usedAmount;

        // Available should never be negative
        $available = max(0, $available);

        $totalAvailable += $available;

        Log::info("-----------------------------------");
        Log::info("Credit #" . ($index + 1) . " (ID: {$credit->id})");
        Log::info("  • Created at: {$credit->created_at}");
        Log::info("  • Total amount: ₹{$credit->amount}");
        Log::info("  • Already used: ₹{$usedAmount}");
        Log::info("  • Available now: ₹{$available}");
        Log::info("  • Still needed: ₹{$remainingAmount}");

        if ($available <= 0) {
            Log::info("  ⏭️ This credit is fully used, skipping...");
            continue;
        }

        if ($remainingAmount <= $available) {
            // This credit can cover the entire remaining amount
            Log::info("  ✅ This credit can cover the full remaining amount!");

            // 🔥 FIX 3: Store which credit we're using and how much
            $usedCredits[] = [
                'credit_id' => $credit->id,
                'amount_used' => $remainingAmount,
                'remaining_in_credit' => $available - $remainingAmount
            ];

            $remainingAmount = 0;
            break;
        } else {
            // This credit can cover part of the amount
            Log::info("  ➡️ Using ₹{$available} from this credit");

            // 🔥 FIX 3: Store partial usage
            $usedCredits[] = [
                'credit_id' => $credit->id,
                'amount_used' => $available,
                'remaining_in_credit' => 0
            ];

            $remainingAmount -= $available;
        }
    }

    Log::info("-----------------------------------");
    Log::info("Total available across all credits: ₹{$totalAvailable}");

    // 🔥 FIX 4: Better validation and return
    if ($remainingAmount > 0) {
        Log::error("❌ INSUFFICIENT BALANCE: Need ₹{$amount}, but only ₹{$totalAvailable} available");
        Log::error("Still need: ₹{$remainingAmount}");
        Log::info("========== 🔍 FIFO ENDED (INSUFFICIENT) ==========");

        // Throw exception instead of returning null
        throw new \Exception('Insufficient wallet balance. Available: ₹' . number_format($totalAvailable, 2));
    }

    if (empty($usedCredits)) {
        Log::error("❌ Could not find suitable wallet despite sufficient balance!");
        Log::info("========== 🔍 FIFO ENDED (NO CREDITS SELECTED) ==========");
        throw new \Exception('System error: Could not determine source wallet');
    }

    // 🔥 FIX 5: Return the FIRST credit ID (oldest) for source_wallet_id
    // Because we're using FIFO, the first credit in our list is the oldest
    $firstUsedCredit = $usedCredits[0]['credit_id'];

    Log::info("✅ FIFO RESULT:");
    Log::info("  • Total amount used: ₹" . ($amount - $remainingAmount));
    Log::info("  • Number of credits used: " . count($usedCredits));
    Log::info("  • Primary source wallet ID: {$firstUsedCredit} (oldest)");

    foreach ($usedCredits as $i => $used) {
        Log::info("  • Credit #{$used['credit_id']}: ₹{$used['amount_used']} used");
    }

    Log::info("========== 🔍 FIFO ENDED (SUCCESS) ==========");

    // Return the OLDEST credit ID that was used
    return $firstUsedCredit;
}
    /**
     * Get current wallet balance
     */
    private function getCurrentWalletBalance($customerId)
    {
        $lastWallet = CustomerWallet::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastWallet ? (float) $lastWallet->balance : 0.00;
    }

    /**
     * Recalculate wallet balance
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
     * Recalculate single invoice status
     */
    private function recalculateInvoiceStatus($saleId)
    {
        $sale = Sale::find($saleId);
        if (!$sale) return;

        // Calculate total paid for this invoice
        $totalPaid = Payment::where('sale_id', $saleId)
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');

        $grandTotal = $sale->grand_total;
        $remaining = $grandTotal - $totalPaid;

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
     * Get success message based on payment type
     */
    private function getSuccessMessage($request, $sale)
    {
        if ($request->is_advance_only == '1') {
            return '💰 Advance payment of ₹' . number_format($request->advance_amount, 2) . ' added to wallet successfully!';
        } elseif ($request->method == 'emi') {
            return '📅 EMI plan created successfully! Down payment: ₹' . number_format($request->down_payment, 2);
        } else {
            $total = ($request->payment_amount ?? 0) + ($request->wallet_used ?? 0);
            return '✅ Payment of ₹' . number_format($total, 2) . ' recorded for Invoice #' . $sale->invoice_no;
        }
    }
}
