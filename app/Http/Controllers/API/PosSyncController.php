<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PosSyncController extends Controller
{
    /**
     * Synchronize an offline invoice to the database.
     */
    public function syncInvoice(Request $request)
    {
        $invoiceData = $request->input('invoice');

        if (!$invoiceData) {
            return response()->json(['success' => false, 'message' => 'No invoice data provided'], 400);
        }

        // Idempotency check: Does this invoice_token already exist?
        $existingSale = Sale::where('invoice_token', $invoiceData['invoice_token'])->first();
        if ($existingSale) {
            return response()->json([
                'success' => true, 
                'message' => 'Invoice already synced', 
                'sale_id' => $existingSale->id
            ]);
        }

        try {
            DB::beginTransaction();

            // 1. Resolve Customer
            $customerData = $invoiceData['customer'];
            $customer = null;

            if (isset($customerData['mobile'])) {
                $customer = Customer::where('mobile', $customerData['mobile'])->first();
            }

            if (!$customer) {
                // Create minimal customer if not found
                $customer = Customer::create([
                    'name' => $customerData['name'] ?? 'Walk-in Customer',
                    'mobile' => $customerData['mobile'] ?? null,
                    'address' => $customerData['address'] ?? null,
                    'open_balance' => 0,
                    'wallet_balance' => 0
                ]);
            }

            // 2. Create Sale
            // Generate invoice_no if not provided (though offline should usually have one)
            $lastSale = Sale::orderBy('id', 'desc')->first();
            $lastId = $lastSale ? $lastSale->id : 0;
            $invoiceNo = 'INV-' . date('Y') . '-' . str_pad(($lastId + 1), 6, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'invoice_no' => $invoiceNo,
                'invoice_token' => $invoiceData['invoice_token'],
                'sale_date' => $invoiceData['created_at'] ?? now(),
                'sub_total' => $invoiceData['totals']['subtotal'],
                'discount' => $invoiceData['totals']['discount'],
                'tax' => $invoiceData['totals']['tax_percent'],
                'tax_amount' => $invoiceData['totals']['tax_amount'],
                'grand_total' => $invoiceData['totals']['grand_total'],
                'payment_status' => 'unpaid', // Usually unpaid until payment sync
                'paid_amount' => 0,
                'requires_shipping' => false // Default for POS
            ]);

            // 3. Create Sale Items & Update Stock
            foreach ($invoiceData['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                    'mrp' => $product ? $product->mrp : $item['price']
                ]);

                if ($product) {
                    $product->decrement('quantity', $item['quantity']);
                }
            }

            DB::commit();

            Log::info("Offline invoice synced successfully: {$sale->invoice_no}", ['token' => $sale->invoice_token]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice synced successfully',
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'error_type' => 'DATABASE_ERROR',
                'message' => 'Database connection failed. Please try again later.'
            ], 500);
        }
    }
}
