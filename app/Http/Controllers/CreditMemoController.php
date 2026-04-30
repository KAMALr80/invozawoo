<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\CreditMemo;
use App\Services\CreditMemoService;
use Illuminate\Http\Request;
use Exception;

class CreditMemoController extends Controller
{
    protected $creditMemoService;

    public function __construct(CreditMemoService $creditMemoService)
    {
        $this->creditMemoService = $creditMemoService;
    }

    /**
     * Show form to create credit memo
     */
    public function create(Sale $sale)
    {
        $sale->load('items.product', 'customer');
        return view('credit_memos.create', compact('sale'));
    }

    /**
     * Store a new credit memo
     */
    public function store(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'type' => 'required|in:full,partial,adjustment',
            'refund_method' => 'required|in:wallet,cash,bank,original_method',
            'restock' => 'boolean',
            'reason' => 'nullable|string',
        ]);

        try {
            $cm = $this->creditMemoService->create($sale, $validated);
            return redirect()->route('sales.show', $sale->id)
                ->with('success', "Credit Memo {$cm->cm_number} created successfully.");
        } catch (Exception $e) {
            \Log::error("Credit Memo Error: " . $e->getMessage(), [
                'sale_id' => $sale->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', "Processing Error: " . $e->getMessage());
        }
    }

    /**
     * Display credit memo details
     */
    public function show(CreditMemo $creditMemo)
    {
        $creditMemo->load('items.product', 'sale', 'customer');
        return view('credit_memos.show', compact('creditMemo'));
    }

    /**
     * List all credit memos
     */
    public function index()
    {
        $creditMemos = CreditMemo::with('customer', 'sale')->latest()->paginate(20);
        $customers = \App\Models\Customer::orderBy('name')->get();
        
        // Calculate stats for the dashboard
        $totalRefunded = CreditMemo::sum('refund_amount');
        $walletCredits = CreditMemo::where('refund_method', 'wallet')->sum('refund_amount');
        $restockedItems = \App\Models\CreditMemoItem::whereHas('creditMemo', function($q) {
            $q->where('restock', 1);
        })->sum('quantity');

        return view('credit_memos.index', compact('creditMemos', 'customers', 'totalRefunded', 'walletCredits', 'restockedItems'));
    }
}
