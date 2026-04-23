<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases
     */
    public function index(Request $request)
    {
        try {
            $query = Purchase::with('product')->latest();

            // Search filter
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('invoice_number', 'like', '%' . $request->search . '%')
                      ->orWhere('supplier_name', 'like', '%' . $request->search . '%')
                      ->orWhereHas('product', function($pq) use ($request) {
                          $pq->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('product_code', 'like', '%' . $request->search . '%');
                      });
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Payment status filter
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('purchase_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('purchase_date', '<=', $request->date_to);
            }

            $purchases = $query->paginate(15);

            // Statistics
            $totalProducts = Product::count();
            $totalPurchaseAmount = Purchase::sum(DB::raw('quantity * price'));
            $lowStockProducts = Product::where('quantity', '<=', 5)->get();

            return view('purchases.index', compact(
                'purchases',
                'totalProducts',
                'totalPurchaseAmount',
                'lowStockProducts'
            ));

        } catch (\Exception $e) {
            Log::error('Purchase index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load purchases: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new purchase
     */
    public function create()
    {
        try {
            $products = Product::orderBy('name')->get();
            return view('purchases.create', compact('products'));
        } catch (\Exception $e) {
            Log::error('Purchase create error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created purchase
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'tax' => 'nullable|numeric|min:0|max:100',
            'purchase_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque',
            'payment_status' => 'required|in:pending,paid,overdue',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'supplier_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:completed,pending,cancelled'
        ]);

        DB::beginTransaction();

        try {
            // Lock product for update
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            // Calculate totals
            $total = $request->quantity * $request->price;
            $discountAmount = ($total * ($request->discount ?? 0)) / 100;
            $afterDiscount = $total - $discountAmount;
            $taxAmount = ($afterDiscount * ($request->tax ?? 0)) / 100;
            $grandTotal = $afterDiscount + $taxAmount;

            // Create purchase record
            $purchase = Purchase::create([
                'product_id' => $request->product_id,
                'user_id' => Auth::id(),
                'invoice_number' => Purchase::generateInvoiceNumber(),
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total' => $total,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'grand_total' => $grandTotal,
                'purchase_date' => $request->purchase_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'supplier_name' => $request->supplier_name,
                'supplier_phone' => $request->supplier_phone,
                'supplier_email' => $request->supplier_email,
                'notes' => $request->notes,
                'status' => $request->status
            ]);

            // Update product stock if purchase is completed
            if ($request->status === 'completed') {
                $product->increment('quantity', $request->quantity);
            }

            DB::commit();

            return redirect()
                ->route('purchases.show', $purchase->id)
                ->with('success', 'Purchase created successfully! Invoice: ' . $purchase->invoice_number);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create purchase: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase
     */
    public function show(Purchase $purchase)
    {
        try {
            // Load the product relationship
            $purchase->load('product');
            
            return view('purchases.show', compact('purchase'));
            
        } catch (\Exception $e) {
            Log::error('Purchase show error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load purchase details.');
        }
    }

    /**
     * Show the form for editing the specified purchase
     */
    public function edit(Purchase $purchase)
    {
        try {
            if ($purchase->status === 'cancelled') {
                return back()->with('error', 'Cannot edit cancelled purchases.');
            }
            
            // Load the product relationship
            $purchase->load('product');
            
            return view('purchases.edit', compact('purchase'));
            
        } catch (\Exception $e) {
            Log::error('Purchase edit error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified purchase
     */
    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'Cannot update cancelled purchases.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'tax' => 'nullable|numeric|min:0|max:100',
            'purchase_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque',
            'payment_status' => 'required|in:pending,paid,overdue',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'supplier_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:completed,pending,cancelled'
        ]);

        DB::beginTransaction();

        try {
            $product = Product::lockForUpdate()->findOrFail($purchase->product_id);
            
            $oldStatus = $purchase->status;
            $oldQuantity = $purchase->quantity;

            // Calculate new totals
            $total = $request->quantity * $request->price;
            $discountAmount = ($total * ($request->discount ?? 0)) / 100;
            $afterDiscount = $total - $discountAmount;
            $taxAmount = ($afterDiscount * ($request->tax ?? 0)) / 100;
            $grandTotal = $afterDiscount + $taxAmount;

            // Update purchase
            $purchase->update([
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total' => $total,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'grand_total' => $grandTotal,
                'purchase_date' => $request->purchase_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'supplier_name' => $request->supplier_name,
                'supplier_phone' => $request->supplier_phone,
                'supplier_email' => $request->supplier_email,
                'notes' => $request->notes,
                'status' => $request->status
            ]);

            // Handle stock adjustments
            if ($oldStatus === 'completed' && $request->status === 'completed') {
                // Both completed - adjust by difference
                $difference = $request->quantity - $oldQuantity;
                if ($difference > 0) {
                    $product->increment('quantity', $difference);
                } elseif ($difference < 0) {
                    $product->decrement('quantity', abs($difference));
                }
            } elseif ($oldStatus === 'completed' && $request->status !== 'completed') {
                // Was completed, now not - remove stock
                $product->decrement('quantity', $oldQuantity);
            } elseif ($oldStatus !== 'completed' && $request->status === 'completed') {
                // Was not completed, now completed - add stock
                $product->increment('quantity', $request->quantity);
            }

            DB::commit();

            return redirect()
                ->route('purchases.show', $purchase->id)
                ->with('success', 'Purchase updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update purchase: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase
     */
    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();

        try {
            // Restore stock if purchase was completed
            if ($purchase->status === 'completed') {
                $product = Product::lockForUpdate()->findOrFail($purchase->product_id);
                $product->decrement('quantity', $purchase->quantity);
            }

            // Delete the purchase
            $purchase->delete();

            DB::commit();

            return redirect()
                ->route('purchases.index')
                ->with('success', 'Purchase deleted successfully! Stock restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase delete error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete purchase.');
        }
    }

    /**
     * Update payment status only
     */
    public function updatePaymentStatus(Request $request, Purchase $purchase)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,overdue',
            'payment_notes' => 'nullable|string|max:500'
        ]);

        try {
            $oldStatus = $purchase->payment_status;
            $purchase->payment_status = $request->payment_status;
            
            if ($request->filled('payment_notes')) {
                $purchase->notes = $purchase->notes . "\n[Payment Update: " . now()->format('d-m-Y H:i') . "] " . $request->payment_notes;
            }
            
            $purchase->save();

            Log::info('Payment status updated', [
                'purchase_id' => $purchase->id,
                'old_status' => $oldStatus,
                'new_status' => $request->payment_status
            ]);

            return back()->with('success', 'Payment status updated successfully!');

        } catch (\Exception $e) {
            Log::error('Payment status update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update payment status.');
        }
    }

    /**
     * Bulk delete purchases
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'purchase_ids' => 'required|array',
            'purchase_ids.*' => 'exists:purchases,id'
        ]);

        DB::beginTransaction();

        try {
            $purchases = Purchase::whereIn('id', $request->purchase_ids)->get();
            $deletedCount = 0;
            
            foreach ($purchases as $purchase) {
                if ($purchase->status === 'completed') {
                    $product = Product::lockForUpdate()->findOrFail($purchase->product_id);
                    $product->decrement('quantity', $purchase->quantity);
                }
                $purchase->delete();
                $deletedCount++;
            }

            DB::commit();

            return redirect()
                ->route('purchases.index')
                ->with('success', $deletedCount . ' purchase(s) deleted successfully! Stock restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete purchases.');
        }
    }

    /**
     * Export purchases (optional method)
     */
    public function export(Request $request)
    {
        try {
            $query = Purchase::with('product');
            
            // Apply filters
            if ($request->filled('date_from')) {
                $query->whereDate('purchase_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('purchase_date', '<=', $request->date_to);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $purchases = $query->get();
            
            // Generate CSV
            $filename = 'purchases_export_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $columns = ['Invoice', 'Date', 'Product', 'Quantity', 'Price', 'Total', 'Status', 'Payment Status'];
            
            $callback = function() use ($purchases, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                
                foreach ($purchases as $purchase) {
                    fputcsv($file, [
                        $purchase->invoice_number,
                        $purchase->purchase_date->format('d-m-Y'),
                        $purchase->product->name ?? 'N/A',
                        $purchase->quantity,
                        $purchase->price,
                        $purchase->grand_total,
                        $purchase->status,
                        $purchase->payment_status
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export purchases.');
        }
    }
}