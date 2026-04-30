<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CreditMemo;
use App\Models\CreditMemoItem;
use App\Models\Product;
use App\Models\CustomerWallet;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class CreditMemoService
{
    /**
     * Create a Credit Memo
     */
    public function create(Sale $sale, array $data)
    {
        return DB::transaction(function () use ($sale, $data) {
            // Lock the sale for update to prevent concurrent modifications
            $sale = Sale::where('id', $sale->id)->lockForUpdate()->first();

            // 1. Basic Validation
            $this->validateReturn($sale, $data['items']);

            // 2. Create Credit Memo Header
            $cm = CreditMemo::create([
                'cm_number' => CreditMemo::generateCMNumber(),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'type' => $data['type'] ?? 'partial',
                'status' => 'approved', // Auto-approve for now or handle via workflow
                'sub_total' => 0, // Will update after items
                'tax_amount' => 0,
                'refund_amount' => 0,
                'reason' => $data['reason'] ?? null,
                'restock' => $data['restock'] ?? true,
                'refund_method' => $data['refund_method'] ?? 'wallet',
                'created_by' => auth()->id() ?? 1,
            ]);

            $totalSubTotal = 0;
            $totalTaxAmount = 0;
            $totalRefundAmount = 0;

            // 3. Process Items
            foreach ($data['items'] as $itemData) {
                if ($itemData['quantity'] <= 0) continue;

                $saleItem = SaleItem::find($itemData['sale_item_id']);
                
                // Calculate line totals
                $unitPrice = $saleItem->price;
                $lineSubTotal = $unitPrice * $itemData['quantity'];
                
                // Pro-rata tax (simplified, should ideally match specific tax logic)
                $originalTaxRate = ($saleItem->total > 0 && $sale->sub_total > 0) ? ($sale->tax_amount / $sale->sub_total) : 0;
                $lineTax = $lineSubTotal * $originalTaxRate;
                
                $lineTotal = $lineSubTotal + $lineTax;

                CreditMemoItem::create([
                    'credit_memo_id' => $cm->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'tax_amount' => $lineTax,
                    'total' => $lineTotal,
                ]);

                // Update Sale Item returned quantity
                $saleItem->increment('returned_quantity', $itemData['quantity']);

                // Handle Inventory
                if ($cm->restock) {
                    $this->restock($saleItem->product, $itemData['quantity'], $cm);
                }

                $totalSubTotal += $lineSubTotal;
                $totalTaxAmount += $lineTax;
                $totalRefundAmount += $lineTotal;
            }

            // 4. Finalize CM Header
            $cm->update([
                'sub_total' => $totalSubTotal,
                'tax_amount' => $totalTaxAmount,
                'refund_amount' => $totalRefundAmount,
            ]);

            // 5. Update Sale Header
            $sale->increment('refunded_amount', $totalRefundAmount);
            $sale->updateStatusBasedOnRefund();

            // 6. Handle Refund (Wallet/Cash)
            $this->processRefundPayment($cm);

            return $cm;
        });
    }

    /**
     * Validate the return request
     */
    protected function validateReturn(Sale $sale, array $items)
    {
        if ($sale->payment_status === 'refunded') {
            throw new Exception("Invoice is already fully refunded.");
        }

        foreach ($items as $itemData) {
            $saleItem = SaleItem::find($itemData['sale_item_id']);
            $availableToReturn = $saleItem->quantity - $saleItem->returned_quantity;

            if ($itemData['quantity'] > $availableToReturn) {
                throw new Exception("Quantity for {$saleItem->product->name} exceeds available amount to return.");
            }
        }
    }

    /**
     * Restock product and log it
     */
    protected function restock(Product $product, $quantity, CreditMemo $cm)
    {
        $product->increment('quantity', $quantity);
        
        // Log in Audit (if applicable) or a dedicated stock ledger
        // AuditLog::create([...]);
    }

    /**
     * Process the actual refund (e.g., adding to customer wallet)
     */
    protected function processRefundPayment(CreditMemo $cm)
    {
        $customer = $cm->customer;
        $amount = $cm->refund_amount;

        if ($cm->refund_method === 'wallet') {
            // 1. Calculate new balance
            $latestWallet = CustomerWallet::where('customer_id', $cm->customer_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $currentBalance = $latestWallet ? $latestWallet->balance : 0;
            $newBalance = $currentBalance + $amount;

            // 2. Create wallet entry (credit) following the system's FIFO pattern
            $wallet = CustomerWallet::create([
                'customer_id' => $cm->customer_id,
                'type' => 'credit',
                'amount' => $amount,
                'balance' => $newBalance,
                'reference' => 'Refund from Credit Memo #' . $cm->cm_number
            ]);

            // 3. Create payment record linked to wallet
            \App\Models\Payment::create([
                'customer_id' => $cm->customer_id,
                'sale_id' => $cm->sale_id,
                'amount' => $amount,
                'method' => 'wallet',
                'status' => 'paid',
                'remarks' => 'REFUND_TO_WALLET',
                'wallet_id' => $wallet->id,
                'source_wallet_id' => $wallet->id, // Mark as a source for future advance usage
            ]);

            // 4. Update customer master balances
            $customer->wallet_balance = $newBalance;
            $customer->open_balance -= $amount; // Refund added to wallet reduces customer's net debt
            $customer->save();
        } else {
            // Handle cash/bank/etc.
            \App\Models\Payment::create([
                'customer_id' => $cm->customer_id,
                'sale_id' => $cm->sale_id,
                'amount' => $amount,
                'method' => $cm->refund_method,
                'status' => 'paid',
                'remarks' => 'REFUND_OUT',
            ]);
            
            // For non-wallet refunds, it's a direct payment out. 
            // In most systems, this also reduces the 'net spent' or 'net due' if we consider returns.
        }
    }
}
