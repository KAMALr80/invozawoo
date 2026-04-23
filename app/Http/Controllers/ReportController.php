<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shipment;
use App\Models\Payment;
use App\Models\CustomerWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DeliveryAgent;
use App\Models\AgentLocation;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    /**
     * ================= SALES REPORTS =================
     */

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $status = $request->get('status', 'all');
        $customerId = $request->get('customer_id');
        $sortBy = $request->get('sort_by', 'sale_date');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();
        $customers = Customer::orderBy('name')->get(['id', 'name']);

        $stats = $this->calculateSalesStats($sales, $startDate, $endDate);

        return view('reports.sales_report', [
            'sales' => $sales,
            'stats' => $stats,
            'customers' => $customers,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => [
                'status' => $status,
                'customer_id' => $customerId,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    public function exportSalesPDF(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$from, $to])
            ->get();

        $stats = $this->calculateSalesStats($sales, $from, $to);

        $data = [
            'sales' => $sales,
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'startDate' => $from,
            'endDate' => $to,
            'generated_date' => now()->format('d M Y, h:i A'),
            'company_name' => config('app.name', 'SmartERP'),
            'filters' => [
                'status' => 'all',
                'customer_id' => null,
            ],
            'customer' => null,
        ];

        $pdf = Pdf::loadView('reports.pdf.sales_report', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
    }

    public function exportSalesReportCSV(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $status = $request->get('status', 'all');
        $customerId = $request->get('customer_id');

        $query = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        $csvData = "Invoice No,Customer Name,Date,Sub Total,Discount,Tax Amount,Grand Total,Payment Status,Items Count\n";

        foreach ($sales as $sale) {
            $itemsCount = $sale->items->sum('quantity');
            $customerName = isset($sale->customer) ? $sale->customer->name : 'Walk-in';
            $csvData .= "\"{$sale->invoice_no}\",";
            $csvData .= "\"{$customerName}\",";
            $csvData .= "\"{$sale->sale_date->format('d-m-Y')}\",";
            $csvData .= "\"{$sale->sub_total}\",";
            $csvData .= "\"{$sale->discount}\",";
            $csvData .= "\"{$sale->tax_amount}\",";
            $csvData .= "\"{$sale->grand_total}\",";
            $csvData .= "\"{$sale->payment_status}\",";
            $csvData .= "\"{$itemsCount}\"\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales_report_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportSalesReportPDF(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $status = $request->get('status', 'all');
        $customerId = $request->get('customer_id');
        $sortBy = $request->get('sort_by', 'sale_date');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy($sortBy, $sortOrder)->get();
        $stats = $this->calculateSalesStats($sales, $startDate, $endDate);
        $customer = !empty($customerId) ? Customer::find($customerId) : null;

        $data = [
            'sales' => $sales,
            'stats' => $stats,
            'customer' => $customer,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => [
                'status' => $status,
                'customer_id' => $customerId,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'generated_date' => now()->format('d M Y, h:i A'),
            'company_name' => config('app.name', 'SmartERP'),
        ];

        $pdf = Pdf::loadView('reports.pdf.sales_report', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * ================= SALES STATS CALCULATION =================
     */

    private function calculateSalesStats($sales, $startDate, $endDate)
    {
        $totalRevenue = $sales->sum('grand_total');
        $totalDiscount = $sales->sum('discount');
        $totalTax = $sales->sum('tax_amount');
        $totalOrders = $sales->count();
        $totalItems = 0;

        foreach ($sales as $sale) {
            $totalItems += $sale->items->sum('quantity');
        }

        $paidAmount = $sales->where('payment_status', 'paid')->sum('grand_total');
        $partialAmount = $sales->where('payment_status', 'partial')->sum('grand_total');
        $unpaidAmount = $sales->where('payment_status', 'unpaid')->sum('grand_total');
        $emiAmount = $sales->where('payment_status', 'emi')->sum('grand_total');

        $paidCount = $sales->where('payment_status', 'paid')->count();
        $partialCount = $sales->where('payment_status', 'partial')->count();
        $unpaidCount = $sales->where('payment_status', 'unpaid')->count();
        $emiCount = $sales->where('payment_status', 'emi')->count();

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'total_orders' => $totalOrders,
            'total_items' => $totalItems,
            'paid_amount' => $paidAmount,
            'partial_amount' => $partialAmount,
            'unpaid_amount' => $unpaidAmount,
            'emi_amount' => $emiAmount,
            'paid_count' => $paidCount,
            'partial_count' => $partialCount,
            'unpaid_count' => $unpaidCount,
            'emi_count' => $emiCount,
            'avg_order_value' => $avgOrderValue,
            'collection_rate' => $totalRevenue > 0 ? round(($paidAmount / $totalRevenue) * 100, 2) : 0,
            'period_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
        ];
    }

    /**
     * ================= CUSTOMERS REPORTS =================
     */

    public function customers(Request $request)
    {
        $customers = Customer::withCount('sales')->get();

        return view('reports.customers', [
            'customers' => $customers,
            'totalCustomers' => $customers->count(),
            'activeCustomers' => $customers->whereNull('deleted_at')->count(),
            'newThisMonth' => $customers->where('created_at', '>=', now()->startOfMonth())->count(),
        ]);
    }

    public function exportCustomersCSV(Request $request)
    {
        $customers = Customer::all();

        $csvData = "Customer ID,Name,Email,Phone,Status,Created\n";

        foreach ($customers as $customer) {
            $status = $customer->deleted_at ? 'Inactive' : 'Active';
            $csvData .= "\"{$customer->id}\",\"{$customer->name}\",\"{$customer->email}\",\"{$customer->mobile}\",\"{$status}\",\"{$customer->created_at->format('d-m-Y')}\"\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers_report_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportCustomersPDF(Request $request)
    {
        $customers = Customer::all();

        $totalCustomers = $customers->count();
        $activeCustomers = $customers->whereNull('deleted_at')->count();
        $totalSales = 0;
        $totalPaid = 0;
        $totalWalletBalance = 0;
        $customersWithDue = 0;

        foreach ($customers as $customer) {
            $customerSales = $customer->sales()->sum('grand_total');
            $customerPaid = $customer->payments()->where('status', 'paid')->sum('amount');
            $customerDue = max(0, $customerSales - $customerPaid);

            $totalSales += $customerSales;
            $totalPaid += $customerPaid;
            $totalWalletBalance += $customer->getCurrentWalletBalanceAttribute();

            if ($customerDue > 0) {
                $customersWithDue++;
            }
        }

        $stats = [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'total_sales' => $totalSales,
            'total_paid' => $totalPaid,
            'total_due' => max(0, $totalSales - $totalPaid),
            'total_wallet_balance' => $totalWalletBalance,
            'customers_with_due' => $customersWithDue,
            'collection_rate' => $totalSales > 0 ? round(($totalPaid / $totalSales) * 100, 2) : 0,
        ];

        $data = [
            'customers' => $customers,
            'stats' => $stats,
            'generated_date' => now()->format('d M Y, h:i A'),
            'company_name' => config('app.name', 'SmartERP'),
            'filters' => [
                'status' => 'all',
                'search' => '',
                'sort_by' => 'name',
                'sort_order' => 'asc',
            ],
        ];

        $pdf = Pdf::loadView('reports.pdf.customers_report', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('customers_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * ================= CUSTOMER DETAILED REPORT =================
     */

    public function customerReport(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $query = Customer::query();

        if ($status === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($status === 'inactive') {
            $query->whereNotNull('deleted_at');
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gst_no', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();

        $stats = $this->calculateCustomerStats($customers);

        return view('reports.customers_report', [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    public function exportCustomerReportCSV(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Customer::query();

        if ($status === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($status === 'inactive') {
            $query->whereNotNull('deleted_at');
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();

        $csvData = "Customer ID,Name,Mobile,Email,GST Number,Address,Total Sales,Total Paid,Total Due,Wallet Balance,Status,Created Date\n";

        foreach ($customers as $customer) {
            $totalSales = $customer->sales()->sum('grand_total');
            $totalPaid = $customer->payments()->where('status', 'paid')->sum('amount');
            $totalDue = max(0, $totalSales - $totalPaid);
            $walletBalance = $customer->getCurrentWalletBalanceAttribute();
            $statusText = $customer->deleted_at ? 'Inactive' : 'Active';

            $csvData .= "\"{$customer->id}\",";
            $csvData .= "\"{$customer->name}\",";
            $csvData .= "\"{$customer->mobile}\",";
            $csvData .= "\"{$customer->email}\",";
            $csvData .= "\"{$customer->gst_no}\",";
            $csvData .= "\"" . str_replace('"', '""', $customer->address ?? '') . "\",";
            $csvData .= "\"{$totalSales}\",";
            $csvData .= "\"{$totalPaid}\",";
            $csvData .= "\"{$totalDue}\",";
            $csvData .= "\"{$walletBalance}\",";
            $csvData .= "\"{$statusText}\",";
            $csvData .= "\"{$customer->created_at->format('d-m-Y')}\"\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer_report_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportCustomerReportPDF(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $query = Customer::query();

        if ($status === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($status === 'inactive') {
            $query->whereNotNull('deleted_at');
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gst_no', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy($sortBy, $sortOrder)->get();
        $stats = $this->calculateCustomerStats($customers);

        $data = [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'generated_date' => now()->format('d M Y, h:i A'),
            'company_name' => config('app.name', 'SmartERP'),
        ];

        $pdf = Pdf::loadView('reports.pdf.customer_report', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('customer_report_' . date('Y-m-d') . '.pdf');
    }

    public function customerSalesReport(Request $request)
    {
        $customerId = $request->get('customer_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(20);

        $customers = Customer::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total_sales' => $sales->sum('grand_total'),
            'total_orders' => $sales->count(),
            'avg_order_value' => $sales->count() > 0 ? $sales->sum('grand_total') / $sales->count() : 0,
            'paid_amount' => $sales->where('payment_status', 'paid')->sum('grand_total'),
            'pending_amount' => $sales->whereIn('payment_status', ['unpaid', 'partial'])->sum('grand_total'),
        ];

        return view('reports.customer_sales_report', [
            'sales' => $sales,
            'stats' => $stats,
            'customers' => $customers,
            'selectedCustomer' => $customerId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function exportCustomerSalesReportCSV(Request $request)
    {
        $customerId = $request->get('customer_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Sale::with('customer')
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        $csvData = "Invoice No,Customer Name,Date,Grand Total,Payment Status,Payment Method\n";

        foreach ($sales as $sale) {
            $csvData .= "\"{$sale->invoice_no}\",";
            $csvData .= "\"{$sale->customer->name}\",";
            $csvData .= "\"{$sale->sale_date->format('d-m-Y')}\",";
            $csvData .= "\"{$sale->grand_total}\",";
            $csvData .= "\"{$sale->payment_status}\",";
            $csvData .= "\"{$sale->payment_method}\"\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer_sales_report_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportCustomerSalesReportPDF(Request $request)
    {
        $customerId = $request->get('customer_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $customer = !empty($customerId) ? Customer::find($customerId) : null;

        $stats = [
            'total_sales' => $sales->sum('grand_total'),
            'total_orders' => $sales->count(),
            'avg_order_value' => $sales->count() > 0 ? $sales->sum('grand_total') / $sales->count() : 0,
            'paid_amount' => $sales->where('payment_status', 'paid')->sum('grand_total'),
            'pending_amount' => $sales->whereIn('payment_status', ['unpaid', 'partial'])->sum('grand_total'),
        ];

        $data = [
            'sales' => $sales,
            'stats' => $stats,
            'customer' => $customer,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generated_date' => now()->format('d M Y, h:i A'),
            'company_name' => config('app.name', 'SmartERP'),
        ];

        $pdf = Pdf::loadView('reports.pdf.customer_sales_report', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('customer_sales_report_' . date('Y-m-d') . '.pdf');
    }

    private function calculateCustomerStats($customers)
    {
        $totalCustomers = $customers->total();
        $activeCustomers = 0;
        $totalSales = 0;
        $totalPaid = 0;
        $totalWalletBalance = 0;
        $customersWithDue = 0;

        foreach ($customers as $customer) {
            if (!$customer->deleted_at) {
                $activeCustomers++;
            }

            $customerSales = $customer->sales()->sum('grand_total');
            $customerPaid = $customer->payments()->where('status', 'paid')->sum('amount');
            $customerDue = max(0, $customerSales - $customerPaid);

            $totalSales += $customerSales;
            $totalPaid += $customerPaid;
            $totalWalletBalance += $customer->getCurrentWalletBalanceAttribute();

            if ($customerDue > 0) {
                $customersWithDue++;
            }
        }

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'inactive_customers' => $totalCustomers - $activeCustomers,
            'total_sales' => $totalSales,
            'total_paid' => $totalPaid,
            'total_due' => max(0, $totalSales - $totalPaid),
            'total_wallet_balance' => $totalWalletBalance,
            'customers_with_due' => $customersWithDue,
            'collection_rate' => $totalSales > 0 ? round(($totalPaid / $totalSales) * 100, 2) : 0,
        ];
    }
/**
 * ================= INVENTORY REPORTS =================
 */

// Add this method for backward compatibility
public function inventory(Request $request)
{
    // Redirect to the new inventoryReport method
    return $this->inventoryReport($request);
}

public function inventoryReport(Request $request)
{
    $category = $request->get('category', 'all');
    $stockStatus = $request->get('stock_status', 'all');
    $search = $request->get('search', '');
    $sortBy = $request->get('sort_by', 'name');
    $sortOrder = $request->get('sort_order', 'asc');

    $query = Product::query();

    // Category filter
    if ($category !== 'all' && !empty($category)) {
        $query->where('category', $category);
    }

    // Stock status filter
    if ($stockStatus === 'low') {
        $query->where('quantity', '<=', 10);
    } elseif ($stockStatus === 'normal') {
        $query->whereBetween('quantity', [11, 30]);
    } elseif ($stockStatus === 'high') {
        $query->where('quantity', '>', 30);
    }

    // Search filter
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('product_code', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    $products = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();
    $categories = Product::distinct()->pluck('category')->filter()->values();

    $stats = $this->calculateInventoryStats($products, $query);

    return view('reports.inventory_report', [
        'products' => $products,
        'stats' => $stats,
        'categories' => $categories,
        'filters' => [
            'category' => $category,
            'stock_status' => $stockStatus,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ],
    ]);
}
/**
 * Export Inventory Report to PDF (for backward compatibility)
 */
public function exportInventoryPDF(Request $request)
{
    return $this->exportInventoryReportPDF($request);
}

/**
 * Export Inventory Report to CSV (for backward compatibility)
 */
public function exportInventoryCSV(Request $request)
{
    return $this->exportInventoryReportCSV($request);
}

// Keep the existing export methods as they are
public function exportInventoryReportCSV(Request $request)
{
    $category = $request->get('category', 'all');
    $stockStatus = $request->get('stock_status', 'all');
    $search = $request->get('search', '');

    $query = Product::query();

    if ($category !== 'all' && !empty($category)) {
        $query->where('category', $category);
    }

    if ($stockStatus === 'low') {
        $query->where('quantity', '<=', 10);
    } elseif ($stockStatus === 'normal') {
        $query->whereBetween('quantity', [11, 30]);
    } elseif ($stockStatus === 'high') {
        $query->where('quantity', '>', 30);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('product_code', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    $products = $query->orderBy('name')->get();

    $csvData = "Product Code,Product Name,Category,Quantity,Price,Stock Value,Status\n";

    foreach ($products as $product) {
        $stockValue = $product->price * $product->quantity;
        $status = $product->quantity <= 10 ? 'Low Stock' : ($product->quantity <= 30 ? 'Normal' : 'High Stock');
        $categoryName = isset($product->category) ? $product->category : 'Uncategorized';

        $csvData .= "\"{$product->product_code}\",";
        $csvData .= "\"{$product->name}\",";
        $csvData .= "\"{$categoryName}\",";
        $csvData .= "\"{$product->quantity}\",";
        $csvData .= "\"{$product->price}\",";
        $csvData .= "\"{$stockValue}\",";
        $csvData .= "\"{$status}\"\n";
    }

    return response($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="inventory_report_' . date('Y-m-d') . '.csv"',
    ]);
}

public function exportInventoryReportPDF(Request $request)
{
    $category = $request->get('category', 'all');
    $stockStatus = $request->get('stock_status', 'all');
    $search = $request->get('search', '');
    $sortBy = $request->get('sort_by', 'name');
    $sortOrder = $request->get('sort_order', 'asc');

    $query = Product::query();

    if ($category !== 'all' && !empty($category)) {
        $query->where('category', $category);
    }

    if ($stockStatus === 'low') {
        $query->where('quantity', '<=', 10);
    } elseif ($stockStatus === 'normal') {
        $query->whereBetween('quantity', [11, 30]);
    } elseif ($stockStatus === 'high') {
        $query->where('quantity', '>', 30);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('product_code', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    $products = $query->orderBy($sortBy, $sortOrder)->get();
    $stats = $this->calculateInventoryStats($products, $query);

    $data = [
        'products' => $products,
        'stats' => $stats,
        'filters' => [
            'category' => $category,
            'stock_status' => $stockStatus,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ],
        'generated_date' => now()->format('d M Y, h:i A'),
        'company_name' => config('app.name', 'SmartERP'),
    ];

    $pdf = Pdf::loadView('reports.pdf.inventory_report', $data);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('inventory_report_' . date('Y-m-d') . '.pdf');
}

private function calculateInventoryStats($products, $query)
{
    $totalProducts = $products->count();
    $totalValue = 0;
    $lowStockCount = 0;
    $normalStockCount = 0;
    $highStockCount = 0;
    $totalQuantity = 0;
    $avgPrice = 0;

    foreach ($products as $product) {
        $totalValue += $product->price * $product->quantity;
        $totalQuantity += $product->quantity;

        if ($product->quantity <= 10) {
            $lowStockCount++;
        } elseif ($product->quantity <= 30) {
            $normalStockCount++;
        } else {
            $highStockCount++;
        }
    }

    $avgPrice = $totalProducts > 0 ? $totalValue / $totalProducts : 0;

    $totalAllProducts = Product::count();
    $totalLowStock = Product::where('quantity', '<=', 10)->count();

    $categoryBreakdown = [];
    $categories = Product::select('category')
        ->selectRaw('COUNT(*) as count')
        ->selectRaw('SUM(quantity) as total_quantity')
        ->selectRaw('SUM(price * quantity) as total_value')
        ->groupBy('category')
        ->get();

    foreach ($categories as $cat) {
        if (isset($cat->category) && !empty($cat->category)) {
            $categoryBreakdown[] = [
                'name' => $cat->category,
                'count' => $cat->count,
                'total_quantity' => $cat->total_quantity,
                'total_value' => $cat->total_value,
            ];
        }
    }

    return [
        'total_products' => $totalProducts,
        'total_value' => $totalValue,
        'total_quantity' => $totalQuantity,
        'avg_price' => $avgPrice,
        'low_stock_count' => $lowStockCount,
        'normal_stock_count' => $normalStockCount,
        'high_stock_count' => $highStockCount,
        'total_all_products' => $totalAllProducts,
        'total_low_stock' => $totalLowStock,
        'category_breakdown' => $categoryBreakdown,
        'stock_health' => $totalProducts > 0 ? round(($highStockCount / $totalProducts) * 100, 2) : 0,
    ];
}
  /**
 * ================= LOGISTICS REPORTS =================
 */

/**
 * Main Logistics Report Page (for backward compatibility)
 */
public function logistics(Request $request)
{
    return $this->logisticsReport($request);
}

/**
 * Export Logistics Report to PDF (for backward compatibility)
 */
public function exportLogisticsPDF(Request $request)
{
    return $this->exportLogisticsReportPDF($request);
}
/**
 * Export Logistics Report to CSV (for backward compatibility)
 */
public function exportLogisticsCSV(Request $request)
{
    return $this->exportLogisticsReportCSV($request);
}
/**
 * Main Logistics Report Page
 */
public function logisticsReport(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $status = $request->get('status', 'all');
    $agentId = $request->get('agent_id');
    $city = $request->get('city', '');

    $query = Shipment::with(['customer', 'deliveryAgent', 'sale'])
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if (!empty($agentId)) {
        $query->where('assigned_to', $agentId);
    }

    if (!empty($city)) {
        $query->where('city', 'like', "%{$city}%");
    }

    $shipments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

    $agents = DeliveryAgent::where('is_active', true)->get(['id', 'name']);

    $stats = $this->calculateLogisticsStats($query, $startDate, $endDate);

    $performanceData = $this->getAgentPerformanceData($startDate, $endDate);
    $cityData = $this->getCityDeliveryData($startDate, $endDate);
    $dailyTrend = $this->getDailyShipmentTrend($startDate, $endDate);
    $statusBreakdown = $this->getStatusBreakdown($startDate, $endDate);

    return view('reports.logistics_report', [
        'shipments' => $shipments,
        'stats' => $stats,
        'agents' => $agents,
        'performanceData' => $performanceData,
        'cityData' => $cityData,
        'dailyTrend' => $dailyTrend,
        'statusBreakdown' => $statusBreakdown,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => [
            'status' => $status,
            'agent_id' => $agentId,
            'city' => $city,
        ],
    ]);
}

/**
 * Export Logistics Report to CSV
 */
public function exportLogisticsReportCSV(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $status = $request->get('status', 'all');
    $agentId = $request->get('agent_id');

    $query = Shipment::with(['customer', 'deliveryAgent', 'sale'])
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if (!empty($agentId)) {
        $query->where('assigned_to', $agentId);
    }

    $shipments = $query->orderBy('created_at', 'desc')->get();

    $csvData = "Shipment Number,Tracking Number,Receiver Name,Receiver Phone,City,State,Pincode,Status,Courier Partner,Shipping Method,Weight,Declared Value,Total Charge,Created Date,Assigned Agent\n";

    foreach ($shipments as $shipment) {
        $agentName = isset($shipment->deliveryAgent) ? $shipment->deliveryAgent->name : 'Unassigned';
        $csvData .= "\"{$shipment->shipment_number}\",";
        $csvData .= "\"{$shipment->tracking_number}\",";
        $csvData .= "\"{$shipment->receiver_name}\",";
        $csvData .= "\"{$shipment->receiver_phone}\",";
        $csvData .= "\"{$shipment->city}\",";
        $csvData .= "\"{$shipment->state}\",";
        $csvData .= "\"{$shipment->pincode}\",";
        $csvData .= "\"{$shipment->status}\",";
        $csvData .= "\"{$shipment->courier_partner}\",";
        $csvData .= "\"{$shipment->shipping_method}\",";
        $csvData .= "\"{$shipment->weight}\",";
        $csvData .= "\"{$shipment->declared_value}\",";
        $csvData .= "\"{$shipment->total_charge}\",";
        $csvData .= "\"{$shipment->created_at->format('d-m-Y H:i')}\",";
        $csvData .= "\"{$agentName}\"\n";
    }

    return response($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="logistics_report_' . date('Y-m-d') . '.csv"',
    ]);
}

/**
 * Export Logistics Report to PDF
 */
public function exportLogisticsReportPDF(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $status = $request->get('status', 'all');
    $agentId = $request->get('agent_id');

    $query = Shipment::with(['customer', 'deliveryAgent', 'sale'])
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if (!empty($agentId)) {
        $query->where('assigned_to', $agentId);
    }

    $shipments = $query->orderBy('created_at', 'desc')->get();
    $stats = $this->calculateLogisticsStats($query, $startDate, $endDate);
    $performanceData = $this->getAgentPerformanceData($startDate, $endDate);
    $cityData = $this->getCityDeliveryData($startDate, $endDate);
    $dailyTrend = $this->getDailyShipmentTrend($startDate, $endDate);
    $statusBreakdown = $this->getStatusBreakdown($startDate, $endDate);
    $agent = !empty($agentId) ? DeliveryAgent::find($agentId) : null;

    $data = [
        'shipments' => $shipments,
        'stats' => $stats,
        'performanceData' => $performanceData,
        'cityData' => $cityData,
        'dailyTrend' => $dailyTrend,
        'statusBreakdown' => $statusBreakdown,
        'agent' => $agent,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => [
            'status' => $status,
            'agent_id' => $agentId,
        ],
        'generated_date' => now()->format('d M Y, h:i A'),
        'company_name' => config('app.name', 'SmartERP'),
    ];

    $pdf = Pdf::loadView('reports.pdf.logistics_report', $data);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('logistics_report_' . date('Y-m-d') . '.pdf');
}

/**
 * Calculate Logistics Statistics
 */
private function calculateLogisticsStats($query, $startDate, $endDate)
{
    $totalShipments = $query->count();
    $delivered = $query->clone()->where('status', 'delivered')->count();
    $inTransit = $query->clone()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->count();
    $pending = $query->clone()->where('status', 'pending')->count();
    $failed = $query->clone()->whereIn('status', ['failed', 'returned'])->count();

    $totalRevenue = $query->clone()->sum('total_charge');
    $totalWeight = $query->clone()->sum('weight');
    $avgDeliveryTime = $this->calculateAverageDeliveryTime($startDate, $endDate);
    $onTimeDeliveryRate = $this->calculateOnTimeDeliveryRate($startDate, $endDate);
    $codShipments = $query->clone()->where('payment_mode', 'cod')->count();

    return [
        'total_shipments' => $totalShipments,
        'delivered' => $delivered,
        'in_transit' => $inTransit,
        'pending' => $pending,
        'failed' => $failed,
        'delivery_rate' => $totalShipments > 0 ? round(($delivered / $totalShipments) * 100, 2) : 0,
        'total_revenue' => $totalRevenue,
        'total_weight' => $totalWeight,
        'avg_delivery_time' => $avgDeliveryTime,
        'on_time_delivery_rate' => $onTimeDeliveryRate,
        'cod_shipments' => $codShipments,
    ];
}

/**
 * Calculate Average Delivery Time
 */
private function calculateAverageDeliveryTime($startDate, $endDate)
{
    $deliveredShipments = Shipment::where('status', 'delivered')
        ->whereBetween('actual_delivery_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->whereNotNull('actual_delivery_date')
        ->get();

    if ($deliveredShipments->isEmpty()) {
        return 0;
    }

    $totalHours = 0;
    foreach ($deliveredShipments as $shipment) {
        $createdAt = Carbon::parse($shipment->created_at);
        $deliveredAt = Carbon::parse($shipment->actual_delivery_date);
        $totalHours += $createdAt->diffInHours($deliveredAt);
    }

    return round($totalHours / $deliveredShipments->count(), 1);
}

/**
 * Calculate On-Time Delivery Rate
 */
private function calculateOnTimeDeliveryRate($startDate, $endDate)
{
    $deliveredShipments = Shipment::where('status', 'delivered')
        ->whereBetween('actual_delivery_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->whereNotNull('estimated_delivery_date')
        ->get();

    if ($deliveredShipments->isEmpty()) {
        return 0;
    }

    $onTimeCount = 0;
    foreach ($deliveredShipments as $shipment) {
        $actualDate = Carbon::parse($shipment->actual_delivery_date);
        $estimatedDate = Carbon::parse($shipment->estimated_delivery_date);
        if ($actualDate <= $estimatedDate) {
            $onTimeCount++;
        }
    }

    return round(($onTimeCount / $deliveredShipments->count()) * 100, 2);
}

/**
 * Get Agent Performance Data
 */
private function getAgentPerformanceData($startDate, $endDate)
{
    return DeliveryAgent::with(['assignedShipments' => function($query) use ($startDate, $endDate) {
        $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
    }])
    ->where('is_active', true)
    ->get()
    ->map(function($agent) {
        $shipments = $agent->assignedShipments;
        $total = $shipments->count();
        $delivered = $shipments->where('status', 'delivered')->count();
        $failed = $shipments->whereIn('status', ['failed', 'returned'])->count();

        return [
            'id' => $agent->id,
            'name' => $agent->name,
            'agent_code' => $agent->agent_code ?? 'AG' . str_pad($agent->id, 4, '0', STR_PAD_LEFT),
            'total_deliveries' => $total,
            'successful_deliveries' => $delivered,
            'failed_deliveries' => $failed,
            'success_rate' => $total > 0 ? round(($delivered / $total) * 100, 2) : 0,
            'rating' => $agent->rating ?? 4.5,
            'vehicle_type' => $agent->vehicle_type ?? 'Bike',
            'city' => $agent->city ?? 'N/A',
        ];
    });
}

/**
 * Get City Delivery Data
 */
private function getCityDeliveryData($startDate, $endDate)
{
    return Shipment::select('city', DB::raw('COUNT(*) as total'), DB::raw('SUM(total_charge) as revenue'))
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->whereNotNull('city')
        ->groupBy('city')
        ->orderBy('total', 'desc')
        ->limit(10)
        ->get()
        ->map(function($city) {
            return [
                'city' => $city->city,
                'total' => $city->total,
                'revenue' => $city->revenue,
            ];
        });
}

/**
 * Get Daily Shipment Trend
 */
private function getDailyShipmentTrend($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    $shipments = Shipment::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
        ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    $trend = [];
    $current = clone $start;

    while ($current <= $end) {
        $dateStr = $current->format('Y-m-d');
        $trend[] = [
            'date' => $current->format('d M'),
            'total' => isset($shipments[$dateStr]) ? $shipments[$dateStr]->total : 0,
        ];
        $current->addDay();
    }

    return $trend;
}

/**
 * Get Status Breakdown
 */
private function getStatusBreakdown($startDate, $endDate)
{
    $statuses = ['pending', 'picked', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'];
    $result = [];

    foreach ($statuses as $status) {
        $count = Shipment::where('status', $status)
            ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $result[] = [
            'status' => $status,
            'count' => $count,
        ];
    }

    return $result;
}


    /**
 * ================= EMPLOYEE REPORTS =================
 */

public function employees(Request $request)
{
  
    return $this->employeeReport($request);
}
public function employeeReport(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $department = $request->get('department', 'all');
    $status = $request->get('status', 'all');
    $search = $request->get('search', '');
    $sortBy = $request->get('sort_by', 'name');
    $sortOrder = $request->get('sort_order', 'asc');

    $query = Employee::with('user')
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($department !== 'all' && !empty($department)) {
        $query->where('department', $department);
    }

    if ($status !== 'all') {
        $query->where('status', $status == 'active' ? 1 : 0);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('employee_code', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    $employees = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();

    $departments = Employee::distinct()->pluck('department')->filter()->values();

    $stats = $this->calculateEmployeeStats($query, $startDate, $endDate);
    $departmentStats = $this->getDepartmentStats($startDate, $endDate);
    $monthlyTrend = $this->getEmployeeMonthlyTrend($startDate, $endDate);
    $statusBreakdown = $this->getEmployeeStatusBreakdown($startDate, $endDate);

    return view('reports.employee_report', [
        'employees' => $employees,
        'stats' => $stats,
        'departments' => $departments,
        'departmentStats' => $departmentStats,
        'monthlyTrend' => $monthlyTrend,
        'statusBreakdown' => $statusBreakdown,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => [
            'department' => $department,
            'status' => $status,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ],
    ]);
}

public function exportEmployeesPDF(Request $request)
{
    return $this->exportEmployeeReportPDF($request);
}

/**
 * Export Employee Report to CSV (for backward compatibility)
 */
public function exportEmployeesCSV(Request $request)
{
    return $this->exportEmployeeReportCSV($request);
}
/**
 * Export Employee Report to CSV
 */
public function exportEmployeeReportCSV(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $department = $request->get('department', 'all');
    $status = $request->get('status', 'all');
    $search = $request->get('search', '');

    $query = Employee::with('user')
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($department !== 'all' && !empty($department)) {
        $query->where('department', $department);
    }

    if ($status !== 'all') {
        $query->where('status', $status == 'active' ? 1 : 0);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('employee_code', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
    }

    $employees = $query->orderBy('name')->get();

    $csvData = "Employee Code,Name,Email,Phone,Department,Role,Joining Date,Status,Created Date\n";

    foreach ($employees as $employee) {
        $role = isset($employee->user) ? ucfirst($employee->user->role) : 'Staff';
        $statusText = $employee->status == 1 ? 'Active' : 'Inactive';
        $joiningDate = isset($employee->joining_date) ? Carbon::parse($employee->joining_date)->format('d-m-Y') : 'N/A';
        $phone = isset($employee->phone) ? $employee->phone : 'N/A';
        $departmentName = isset($employee->department) ? $employee->department : 'Not Assigned';

        $csvData .= "\"{$employee->employee_code}\",";
        $csvData .= "\"{$employee->name}\",";
        $csvData .= "\"{$employee->email}\",";
        $csvData .= "\"{$phone}\",";
        $csvData .= "\"{$departmentName}\",";
        $csvData .= "\"{$role}\",";
        $csvData .= "\"{$joiningDate}\",";
        $csvData .= "\"{$statusText}\",";
        $csvData .= "\"{$employee->created_at->format('d-m-Y')}\"\n";
    }

    return response($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="employee_report_' . date('Y-m-d') . '.csv"',
    ]);
}

/**
 * Export Employee Report to PDF
 */
public function exportEmployeeReportPDF(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $department = $request->get('department', 'all');
    $status = $request->get('status', 'all');
    $search = $request->get('search', '');
    $sortBy = $request->get('sort_by', 'name');
    $sortOrder = $request->get('sort_order', 'asc');

    $query = Employee::with('user')
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($department !== 'all' && !empty($department)) {
        $query->where('department', $department);
    }

    if ($status !== 'all') {
        $query->where('status', $status == 'active' ? 1 : 0);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('employee_code', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
    }

    $employees = $query->orderBy($sortBy, $sortOrder)->get();
    $stats = $this->calculateEmployeeStats($query, $startDate, $endDate);
    $departmentStats = $this->getDepartmentStats($startDate, $endDate);
    $monthlyTrend = $this->getEmployeeMonthlyTrend($startDate, $endDate);
    $statusBreakdown = $this->getEmployeeStatusBreakdown($startDate, $endDate);

    $data = [
        'employees' => $employees,
        'stats' => $stats,
        'departmentStats' => $departmentStats,
        'monthlyTrend' => $monthlyTrend,
        'statusBreakdown' => $statusBreakdown,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => [
            'department' => $department,
            'status' => $status,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ],
        'generated_date' => now()->format('d M Y, h:i A'),
        'company_name' => config('app.name', 'SmartERP'),
    ];

    $pdf = Pdf::loadView('reports.pdf.employee_report', $data);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('employee_report_' . date('Y-m-d') . '.pdf');
}

/**
 * Calculate Employee Statistics
 */
private function calculateEmployeeStats($query, $startDate, $endDate)
{
    $totalEmployees = $query->count();
    $activeEmployees = $query->clone()->where('status', 1)->count();
    $inactiveEmployees = $totalEmployees - $activeEmployees;

    $departmentCount = Employee::distinct('department')->whereNotNull('department')->count();
    $newHires = $query->clone()->whereBetween('joining_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])->count();

    $roles = [
        'admin' => 0,
        'hr' => 0,
        'staff' => 0,
    ];

    foreach ($query->get() as $employee) {
        if (isset($employee->user)) {
            $role = $employee->user->role;
            if (isset($roles[$role])) {
                $roles[$role]++;
            }
        } else {
            $roles['staff']++;
        }
    }

    return [
        'total_employees' => $totalEmployees,
        'active_employees' => $activeEmployees,
        'inactive_employees' => $inactiveEmployees,
        'department_count' => $departmentCount,
        'new_hires' => $newHires,
        'active_rate' => $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 2) : 0,
        'role_breakdown' => $roles,
    ];
}
/**
 * Get Department Statistics
 */
private function getDepartmentStats($startDate, $endDate)
{
    $departments = Employee::select('department', DB::raw('COUNT(*) as count'))
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->whereNotNull('department')
        ->groupBy('department')
        ->orderBy('count', 'desc')
        ->get();

    $result = [];
    foreach ($departments as $dept) {
        $result[] = [
            'department' => $dept->department,
            'count' => $dept->count,
        ];
    }

    return $result;
}

/**
 * Get Employee Monthly Trend
 */
private function getEmployeeMonthlyTrend($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    $employees = Employee::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
        ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    $trend = [];
    $current = clone $start;

    while ($current <= $end) {
        $dateStr = $current->format('Y-m-d');
        $count = isset($employees[$dateStr]) ? $employees[$dateStr]->total : 0;
        $trend[] = [
            'date' => $current->format('d M'),
            'total' => $count,
        ];
        $current->addDay();
    }

    return $trend;
}

/**
 * Get Employee Status Breakdown
 */
/**
 * Get Employee Status Breakdown
 */
private function getEmployeeStatusBreakdown($startDate, $endDate)
{
    $active = Employee::where('status', 1)
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->count();

    $inactive = Employee::where('status', 0)
        ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->count();

    $result = [];
    $result[] = ['status' => 'active', 'count' => $active];
    $result[] = ['status' => 'inactive', 'count' => $inactive];

    return $result;
}
 /**
 * ================= PURCHASE REPORTS =================
 */

/**
 * Purchase Report (for backward compatibility with existing routes)
 */
public function purchases(Request $request)
{
    return $this->purchaseReport($request);
}

/**
 * Main Purchase Report Page
 */
public function purchaseReport(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $status = $request->get('status', 'all');
    $paymentStatus = $request->get('payment_status', 'all');
    $supplier = $request->get('supplier', '');
    $search = $request->get('search', '');
    $sortBy = $request->get('sort_by', 'purchase_date');
    $sortOrder = $request->get('sort_order', 'desc');

    $query = Purchase::with('product')
        ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($paymentStatus !== 'all') {
        $query->where('payment_status', $paymentStatus);
    }

    if (!empty($supplier)) {
        $query->where('supplier_name', 'like', "%{$supplier}%");
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('supplier_name', 'like', "%{$search}%")
              ->orWhereHas('product', function($pq) use ($search) {
                  $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
              });
        });
    }

    $purchases = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();

    $stats = $this->calculatePurchaseStats($query, $startDate, $endDate);
    $supplierStats = $this->getSupplierStats($startDate, $endDate);
    $monthlyTrend = $this->getPurchaseMonthlyTrend($startDate, $endDate);
    $statusBreakdown = $this->getPurchaseStatusBreakdown($startDate, $endDate);
    $paymentBreakdown = $this->getPurchasePaymentBreakdown($startDate, $endDate);

    return view('reports.purchase_report', [
        'purchases' => $purchases,
        'stats' => $stats,
        'supplierStats' => $supplierStats,
        'monthlyTrend' => $monthlyTrend,
        'statusBreakdown' => $statusBreakdown,
        'paymentBreakdown' => $paymentBreakdown,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => [
            'status' => $status,
            'payment_status' => $paymentStatus,
            'supplier' => $supplier,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ],
    ]);
}

/**
 * Export Purchase Report to CSV (for backward compatibility)
 */
public function exportPurchasesCSV(Request $request)
{
    return $this->exportPurchaseReportCSV($request);
}

/**
 * Export Purchase Report to CSV
 */
public function exportPurchaseReportCSV(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $status = $request->get('status', 'all');
    $paymentStatus = $request->get('payment_status', 'all');
    $supplier = $request->get('supplier', '');
    $search = $request->get('search', '');

    $query = Purchase::with('product')
        ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($paymentStatus !== 'all') {
        $query->where('payment_status', $paymentStatus);
    }

    if (!empty($supplier)) {
        $query->where('supplier_name', 'like', "%{$supplier}%");
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('supplier_name', 'like', "%{$search}%")
              ->orWhereHas('product', function($pq) use ($search) {
                  $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
              });
        });
    }

    $purchases = $query->orderBy('purchase_date', 'desc')->get();

    $csvData = "Invoice Number,Purchase Date,Product Name,Product Code,Quantity,Unit Price,Subtotal,Discount %,Discount Amount,Tax %,Tax Amount,Grand Total,Supplier Name,Supplier Phone,Supplier Email,Payment Method,Payment Status,Purchase Status,Notes\n";

    foreach ($purchases as $purchase) {
        $discountAmount = ($purchase->total * (isset($purchase->discount) ? $purchase->discount : 0)) / 100;
        $afterDiscount = $purchase->total - $discountAmount;
        $taxAmount = ($afterDiscount * (isset($purchase->tax) ? $purchase->tax : 0)) / 100;

        $productName = isset($purchase->product) ? $purchase->product->name : 'N/A';
        $productCode = (isset($purchase->product) && isset($purchase->product->product_code)) ? $purchase->product->product_code : 'N/A';
        $supplierName = isset($purchase->supplier_name) ? $purchase->supplier_name : '';
        $supplierPhone = isset($purchase->supplier_phone) ? $purchase->supplier_phone : '';
        $supplierEmail = isset($purchase->supplier_email) ? $purchase->supplier_email : '';
        $notes = isset($purchase->notes) ? str_replace('"', '""', $purchase->notes) : '';

        $csvData .= "\"{$purchase->invoice_number}\",";
        $csvData .= "\"{$purchase->purchase_date->format('d-m-Y')}\",";
        $csvData .= "\"{$productName}\",";
        $csvData .= "\"{$productCode}\",";
        $csvData .= "\"{$purchase->quantity}\",";
        $csvData .= "\"{$purchase->price}\",";
        $csvData .= "\"{$purchase->total}\",";
        $csvData .= "\"" . (isset($purchase->discount) ? $purchase->discount : 0) . "\",";
        $csvData .= "\"{$discountAmount}\",";
        $csvData .= "\"" . (isset($purchase->tax) ? $purchase->tax : 0) . "\",";
        $csvData .= "\"{$taxAmount}\",";
        $csvData .= "\"{$purchase->grand_total}\",";
        $csvData .= "\"{$supplierName}\",";
        $csvData .= "\"{$supplierPhone}\",";
        $csvData .= "\"{$supplierEmail}\",";
        $csvData .= "\"{$purchase->payment_method}\",";
        $csvData .= "\"{$purchase->payment_status}\",";
        $csvData .= "\"{$purchase->status}\",";
        $csvData .= "\"{$notes}\"\n";
    }

    return response($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="purchase_report_' . date('Y-m-d') . '.csv"',
    ]);
}

/**
 * Export Purchase Report to PDF (for backward compatibility)
 */
public function exportPurchasesPDF(Request $request)
{
    return $this->exportPurchaseReportPDF($request);
}

/**
 * Export Purchase Report to PDF
 */
public function exportPurchaseReportPDF(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $status = $request->get('status', 'all');
    $paymentStatus = $request->get('payment_status', 'all');
    $supplier = $request->get('supplier', '');
    $search = $request->get('search', '');
    $sortBy = $request->get('sort_by', 'purchase_date');
    $sortOrder = $request->get('sort_order', 'desc');

    $query = Purchase::with('product')
        ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($paymentStatus !== 'all') {
        $query->where('payment_status', $paymentStatus);
    }

    if (!empty($supplier)) {
        $query->where('supplier_name', 'like', "%{$supplier}%");
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('supplier_name', 'like', "%{$search}%")
              ->orWhereHas('product', function($pq) use ($search) {
                  $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
              });
        });
    }

    $purchases = $query->orderBy($sortBy, $sortOrder)->get();
    $stats = $this->calculatePurchaseStats($query, $startDate, $endDate);
    $supplierStats = $this->getSupplierStats($startDate, $endDate);
    $monthlyTrend = $this->getPurchaseMonthlyTrend($startDate, $endDate);
    $statusBreakdown = $this->getPurchaseStatusBreakdown($startDate, $endDate);
    $paymentBreakdown = $this->getPurchasePaymentBreakdown($startDate, $endDate);

    $data = [
        'purchases' => $purchases,
        'stats' => $stats,
        'supplierStats' => $supplierStats,
        'monthlyTrend' => $monthlyTrend,
        'statusBreakdown' => $statusBreakdown,
        'paymentBreakdown' => $paymentBreakdown,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => [
            'status' => $status,
            'payment_status' => $paymentStatus,
            'supplier' => $supplier,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ],
        'generated_date' => now()->format('d M Y, h:i A'),
        'company_name' => config('app.name', 'SmartERP'),
    ];

    $pdf = Pdf::loadView('reports.pdf.purchase_report', $data);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('purchase_report_' . date('Y-m-d') . '.pdf');
}

/**
 * Calculate Purchase Statistics
 */
private function calculatePurchaseStats($query, $startDate, $endDate)
{
    $totalPurchases = $query->count();
    $totalSpent = $query->clone()->sum('grand_total');
    $totalQuantity = $query->clone()->sum('quantity');
    $avgPurchaseValue = $totalPurchases > 0 ? $totalSpent / $totalPurchases : 0;

    $completed = $query->clone()->where('status', 'completed')->count();
    $pending = $query->clone()->where('status', 'pending')->count();
    $cancelled = $query->clone()->where('status', 'cancelled')->count();

    $paid = $query->clone()->where('payment_status', 'paid')->count();
    $paymentPending = $query->clone()->where('payment_status', 'pending')->count();
    $overdue = $query->clone()->where('payment_status', 'overdue')->count();

    $uniqueSuppliers = Purchase::whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->whereNotNull('supplier_name')
        ->distinct('supplier_name')
        ->count('supplier_name');

    return [
        'total_purchases' => $totalPurchases,
        'total_spent' => $totalSpent,
        'total_quantity' => $totalQuantity,
        'avg_purchase_value' => $avgPurchaseValue,
        'completed' => $completed,
        'pending' => $pending,
        'cancelled' => $cancelled,
        'paid' => $paid,
        'payment_pending' => $paymentPending,
        'overdue' => $overdue,
        'unique_suppliers' => $uniqueSuppliers,
        'completion_rate' => $totalPurchases > 0 ? round(($completed / $totalPurchases) * 100, 2) : 0,
        'payment_rate' => $totalPurchases > 0 ? round(($paid / $totalPurchases) * 100, 2) : 0,
    ];
}

/**
 * Get Supplier Statistics
 */
private function getSupplierStats($startDate, $endDate)
{
    $suppliers = Purchase::select('supplier_name', DB::raw('COUNT(*) as total_purchases'), DB::raw('SUM(grand_total) as total_amount'))
        ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
        ->whereNotNull('supplier_name')
        ->groupBy('supplier_name')
        ->orderBy('total_amount', 'desc')
        ->limit(10)
        ->get();

    $result = array();
    foreach ($suppliers as $supplier) {
        $result[] = array(
            'name' => $supplier->supplier_name,
            'total_purchases' => $supplier->total_purchases,
            'total_amount' => $supplier->total_amount,
            'avg_amount' => $supplier->total_purchases > 0 ? $supplier->total_amount / $supplier->total_purchases : 0,
        );
    }

    return $result;
}

/**
 * Get Purchase Monthly Trend
 */
private function getPurchaseMonthlyTrend($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    $purchases = Purchase::select(DB::raw('DATE(purchase_date) as date'), DB::raw('COUNT(*) as total'), DB::raw('SUM(grand_total) as amount'))
        ->whereBetween('purchase_date', [$start->startOfDay(), $end->endOfDay()])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    $trend = array();
    $current = clone $start;

    while ($current <= $end) {
        $dateStr = $current->format('Y-m-d');
        $count = isset($purchases[$dateStr]) ? $purchases[$dateStr]->total : 0;
        $amount = isset($purchases[$dateStr]) ? $purchases[$dateStr]->amount : 0;
        $trend[] = array(
            'date' => $current->format('d M'),
            'total' => $count,
            'amount' => $amount,
        );
        $current->addDay();
    }

    return $trend;
}

/**
 * Get Purchase Status Breakdown
 */
private function getPurchaseStatusBreakdown($startDate, $endDate)
{
    $statuses = array('completed', 'pending', 'cancelled');
    $result = array();

    foreach ($statuses as $status) {
        $count = Purchase::where('status', $status)
            ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $amount = Purchase::where('status', $status)
            ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->sum('grand_total');

        $result[] = array(
            'status' => $status,
            'count' => $count,
            'amount' => $amount,
        );
    }

    return $result;
}

/**
 * Get Purchase Payment Breakdown
 */
private function getPurchasePaymentBreakdown($startDate, $endDate)
{
    $statuses = array('paid', 'pending', 'overdue');
    $result = array();

    foreach ($statuses as $status) {
        $count = Purchase::where('payment_status', $status)
            ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $amount = Purchase::where('payment_status', $status)
            ->whereBetween('purchase_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->sum('grand_total');

        $result[] = array(
            'status' => $status,
            'count' => $count,
            'amount' => $amount,
        );
    }

    return $result;
}


   /**
 * ================= ATTENDANCE REPORTS =================
 */

/**
 * Main Attendance Report Page
 */
public function attendanceReport(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $employeeId = $request->get('employee_id');
    $status = $request->get('status', 'all');
    $department = $request->get('department', 'all');
    $sortBy = $request->get('sort_by', 'attendance_date');
    $sortOrder = $request->get('sort_order', 'desc');

    $query = Attendance::with('employee')
        ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if (!empty($employeeId)) {
        $query->where('employee_id', $employeeId);
    }

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($department !== 'all') {
        $query->whereHas('employee', function($q) use ($department) {
            $q->where('department', $department);
        });
    }

    $attendances = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();

    $employees = Employee::where('status', 'active')->get(['id', 'name', 'employee_code', 'department']);
    $departments = Employee::distinct()->pluck('department')->filter()->values();

    $stats = $this->calculateAttendanceStats($query, $startDate, $endDate);
    $employeeStats = $this->getEmployeeAttendanceStats($startDate, $endDate);
    $dailyTrend = $this->getAttendanceDailyTrend($startDate, $endDate);
    $statusBreakdown = $this->getAttendanceStatusBreakdown($startDate, $endDate);
    $departmentStats = $this->getAttendanceDepartmentStats($startDate, $endDate);

    return view('reports.attendance_report', [
        'attendances' => $attendances,
        'stats' => $stats,
        'employeeStats' => $employeeStats,
        'dailyTrend' => $dailyTrend,
        'statusBreakdown' => $statusBreakdown,
        'departmentStats' => $departmentStats,
        'employees' => $employees,
        'departments' => $departments,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => array(
            'employee_id' => $employeeId,
            'status' => $status,
            'department' => $department,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ),
    ]);
}

/**
 * Export Attendance Report to CSV
 */
public function exportAttendanceReportCSV(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $employeeId = $request->get('employee_id');
    $status = $request->get('status', 'all');
    $department = $request->get('department', 'all');

    $query = Attendance::with('employee')
        ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if (!empty($employeeId)) {
        $query->where('employee_id', $employeeId);
    }

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($department !== 'all') {
        $query->whereHas('employee', function($q) use ($department) {
            $q->where('department', $department);
        });
    }

    $attendances = $query->orderBy('attendance_date', 'desc')->get();

    $csvData = "Employee Name,Employee Code,Department,Date,Status,Check In,Check Out,Working Hours,Remarks\n";

    foreach ($attendances as $attendance) {
        $employeeName = isset($attendance->employee) ? $attendance->employee->name : 'N/A';
        $employeeCode = isset($attendance->employee) ? $attendance->employee->employee_code : 'N/A';
        $departmentName = (isset($attendance->employee) && isset($attendance->employee->department)) ? $attendance->employee->department : 'N/A';
        $checkIn = isset($attendance->check_in) ? $attendance->check_in : '-';
        $checkOut = isset($attendance->check_out) ? $attendance->check_out : '-';
        $workingHours = isset($attendance->working_hours) ? $attendance->working_hours : '-';
        $remarks = isset($attendance->remarks) ? str_replace('"', '""', $attendance->remarks) : '';

        $csvData .= "\"{$employeeName}\",";
        $csvData .= "\"{$employeeCode}\",";
        $csvData .= "\"{$departmentName}\",";
        $csvData .= "\"{$attendance->attendance_date->format('d-m-Y')}\",";
        $csvData .= "\"{$attendance->status}\",";
        $csvData .= "\"{$checkIn}\",";
        $csvData .= "\"{$checkOut}\",";
        $csvData .= "\"{$workingHours}\",";
        $csvData .= "\"{$remarks}\"\n";
    }

    return response($csvData, 200, array(
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="attendance_report_' . date('Y-m-d') . '.csv"',
    ));
}

/**
 * Export Attendance Report to PDF
 */
public function exportAttendanceReportPDF(Request $request)
{
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $employeeId = $request->get('employee_id');
    $status = $request->get('status', 'all');
    $department = $request->get('department', 'all');
    $sortBy = $request->get('sort_by', 'attendance_date');
    $sortOrder = $request->get('sort_order', 'desc');

    $query = Attendance::with('employee')
        ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

    if (!empty($employeeId)) {
        $query->where('employee_id', $employeeId);
    }

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($department !== 'all') {
        $query->whereHas('employee', function($q) use ($department) {
            $q->where('department', $department);
        });
    }

    $attendances = $query->orderBy($sortBy, $sortOrder)->get();
    $stats = $this->calculateAttendanceStats($query, $startDate, $endDate);
    $employeeStats = $this->getEmployeeAttendanceStats($startDate, $endDate);
    $dailyTrend = $this->getAttendanceDailyTrend($startDate, $endDate);
    $statusBreakdown = $this->getAttendanceStatusBreakdown($startDate, $endDate);
    $departmentStats = $this->getAttendanceDepartmentStats($startDate, $endDate);
    $selectedEmployee = (!empty($employeeId)) ? Employee::find($employeeId) : null;

    $data = array(
        'attendances' => $attendances,
        'stats' => $stats,
        'employeeStats' => $employeeStats,
        'dailyTrend' => $dailyTrend,
        'statusBreakdown' => $statusBreakdown,
        'departmentStats' => $departmentStats,
        'selectedEmployee' => $selectedEmployee,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'filters' => array(
            'employee_id' => $employeeId,
            'status' => $status,
            'department' => $department,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ),
        'generated_date' => now()->format('d M Y, h:i A'),
        'company_name' => config('app.name', 'SmartERP'),
    );

    $pdf = Pdf::loadView('reports.pdf.attendance_report', $data);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('attendance_report_' . date('Y-m-d') . '.pdf');
}

/**
 * Calculate Attendance Statistics
 */
private function calculateAttendanceStats($query, $startDate, $endDate)
{
    $totalRecords = $query->count();
    $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

    $present = $query->clone()->where('status', 'Present')->count();
    $absent = $query->clone()->where('status', 'Absent')->count();
    $late = $query->clone()->where('status', 'Late')->count();
    $halfDay = $query->clone()->where('status', 'Half Day')->count();
    $leave = $query->clone()->where('status', 'Leave')->count();

    $totalEmployees = Employee::where('status', 'active')->count();
    $avgAttendanceRate = ($totalRecords > 0) ? round(($present / $totalRecords) * 100, 2) : 0;
    $avgLateRate = ($totalRecords > 0) ? round(($late / $totalRecords) * 100, 2) : 0;

    // Calculate average working hours
    $avgWorkingHours = '00:00:00';
    $workingHoursRecords = $query->clone()->whereNotNull('working_hours')->get();
    if ($workingHoursRecords->count() > 0) {
        $totalSeconds = 0;
        foreach ($workingHoursRecords as $record) {
            if (!empty($record->working_hours)) {
                $parts = explode(':', $record->working_hours);
                $seconds = (int)($parts[0] * 3600) + (int)($parts[1] * 60) + (int)(isset($parts[2]) ? $parts[2] : 0);
                $totalSeconds += $seconds;
            }
        }
        $avgSeconds = $totalSeconds / $workingHoursRecords->count();
        $avgWorkingHours = gmdate('H:i:s', $avgSeconds);
    }

    return array(
        'total_records' => $totalRecords,
        'total_days' => $totalDays,
        'present' => $present,
        'absent' => $absent,
        'late' => $late,
        'half_day' => $halfDay,
        'leave' => $leave,
        'avg_attendance_rate' => $avgAttendanceRate,
        'avg_late_rate' => $avgLateRate,
        'avg_working_hours' => $avgWorkingHours,
        'total_employees' => $totalEmployees,
    );
}

/**
 * Get Employee Attendance Statistics
 */
private function getEmployeeAttendanceStats($startDate, $endDate)
{
    $employees = Employee::where('status', 'active')->get();
    $result = array();

    foreach ($employees as $employee) {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->get();

        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $present = $attendances->where('status', 'Present')->count();
        $absent = $attendances->where('status', 'Absent')->count();
        $late = $attendances->where('status', 'Late')->count();
        $halfDay = $attendances->where('status', 'Half Day')->count();
        $leave = $attendances->where('status', 'Leave')->count();

        $attendanceRate = ($totalDays > 0) ? round(($present / $totalDays) * 100, 2) : 0;

        $result[] = array(
            'id' => $employee->id,
            'name' => $employee->name,
            'employee_code' => $employee->employee_code,
            'department' => isset($employee->department) ? $employee->department : 'N/A',
            'total_days' => $totalDays,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'leave' => $leave,
            'attendance_rate' => $attendanceRate,
        );
    }

    return $result;
}

/**
 * Get Attendance Daily Trend
 */
private function getAttendanceDailyTrend($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    $attendances = Attendance::select(DB::raw('DATE(attendance_date) as date'),
        DB::raw('COUNT(*) as total'),
        DB::raw('SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present'),
        DB::raw('SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent'),
        DB::raw('SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late'),
        DB::raw('SUM(CASE WHEN status = "Half Day" THEN 1 ELSE 0 END) as half_day'))
        ->whereBetween('attendance_date', [$start->startOfDay(), $end->endOfDay()])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    $trend = array();
    $current = clone $start;

    while ($current <= $end) {
        $dateStr = $current->format('Y-m-d');
        $data = isset($attendances[$dateStr]) ? $attendances[$dateStr] : null;

        $trend[] = array(
            'date' => $current->format('d M'),
            'present' => $data ? $data->present : 0,
            'absent' => $data ? $data->absent : 0,
            'late' => $data ? $data->late : 0,
            'half_day' => $data ? $data->half_day : 0,
            'total' => $data ? $data->total : 0,
        );
        $current->addDay();
    }

    return $trend;
}

/**
 * Get Attendance Status Breakdown
 */
private function getAttendanceStatusBreakdown($startDate, $endDate)
{
    $statuses = array('Present', 'Absent', 'Late', 'Half Day', 'Leave');
    $result = array();

    foreach ($statuses as $status) {
        $count = Attendance::where('status', $status)
            ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $result[] = array(
            'status' => $status,
            'count' => $count,
        );
    }

    return $result;
}

/**
 * Get Attendance Department Statistics
 */
private function getAttendanceDepartmentStats($startDate, $endDate)
{
    $departments = Employee::whereNotNull('department')
        ->distinct()
        ->pluck('department');

    $result = array();

    foreach ($departments as $dept) {
        $employeeIds = Employee::where('department', $dept)->pluck('id');

        $present = Attendance::whereIn('employee_id', $employeeIds)
            ->where('status', 'Present')
            ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $total = Attendance::whereIn('employee_id', $employeeIds)
            ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $late = Attendance::whereIn('employee_id', $employeeIds)
            ->where('status', 'Late')
            ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $absent = Attendance::whereIn('employee_id', $employeeIds)
            ->where('status', 'Absent')
            ->whereBetween('attendance_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->count();

        $attendanceRate = ($total > 0) ? round(($present / $total) * 100, 2) : 0;

        $result[] = array(
            'department' => $dept,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'total' => $total,
            'attendance_rate' => $attendanceRate,
        );
    }

    return $result;
}

/**
 * Attendance Report (for backward compatibility)
 */
public function attendance(Request $request)
{
    return $this->attendanceReport($request);
}

/**
 * Export Attendance Report to PDF (for backward compatibility)
 */
public function exportAttendancePDF(Request $request)
{
    return $this->exportAttendanceReportPDF($request);
}

/**
 * Export Attendance Report to CSV (for backward compatibility)
 */
public function exportAttendanceCSV(Request $request)
{
    return $this->exportAttendanceReportCSV($request);
}

    /**
     * ================= FINANCIAL REPORTS =================
     */

    public function financial(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now());

        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
        $purchases = Purchase::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalRevenue = $sales->sum('grand_total');
        $totalExpenses = $purchases->sum('total');
        $netProfit = $totalRevenue - $totalExpenses;
        $netProfitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        return view('reports.financial', [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'netProfitMargin' => $netProfitMargin,
            'totalSales' => $totalRevenue,
            'totalOrders' => $sales->count(),
            'avgOrderValue' => $sales->count() > 0 ? $totalRevenue / $sales->count() : 0,
            'totalPurchases' => $totalExpenses,
            'totalPurchaseOrders' => $purchases->count(),
            'avgPurchaseValue' => $purchases->count() > 0 ? $totalExpenses / $purchases->count() : 0,
            'amountReceived' => $sales->where('payment_status', 'paid')->sum('grand_total'),
            'outstandingAmount' => $sales->where('payment_status', '!=', 'paid')->sum('grand_total'),
            'collectionRate' => $totalRevenue > 0 ? round((($sales->where('payment_status', 'paid')->sum('grand_total') / $totalRevenue) * 100), 2) : 0,
            'operatingCashFlow' => $netProfit,
            'liquidityRatio' => '1.5',
            'debtRatio' => '32',
        ]);
    }

    public function exportFinancialCSV(Request $request)
    {
        $sales = Sale::all();
        $purchases = Purchase::all();

        $csvData = "Financial Summary Report\n";
        $csvData .= "Generated: " . now()->format('d-m-Y H:i:s') . "\n\n";
        $csvData .= "Total Revenue,Total Expenses,Net Profit,Profit Margin %\n";
        $profitMargin = $sales->sum('grand_total') > 0 ? round((($sales->sum('grand_total') - $purchases->sum('total')) / $sales->sum('grand_total')) * 100, 2) : 0;
        $csvData .= "\"{$sales->sum('grand_total')}\",\"{$purchases->sum('total')}\",\"" . ($sales->sum('grand_total') - $purchases->sum('total')) . "\",\"{$profitMargin}\"\n";

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="financial_report_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportFinancialPDF(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now());

        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
        $purchases = Purchase::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalRevenue = $sales->sum('grand_total');
        $totalExpenses = $purchases->sum('total');
        $netProfit = $totalRevenue - $totalExpenses;
        $netProfitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        $data = [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'netProfitMargin' => $netProfitMargin,
            'totalSales' => $totalRevenue,
            'totalOrders' => $sales->count(),
            'avgOrderValue' => $sales->count() > 0 ? $totalRevenue / $sales->count() : 0,
            'totalPurchases' => $totalExpenses,
            'totalPurchaseOrders' => $purchases->count(),
            'avgPurchaseValue' => $purchases->count() > 0 ? $totalExpenses / $purchases->count() : 0,
            'amountReceived' => $sales->where('payment_status', 'paid')->sum('grand_total'),
            'outstandingAmount' => $sales->where('payment_status', '!=', 'paid')->sum('grand_total'),
            'collectionRate' => $totalRevenue > 0 ? round((($sales->where('payment_status', 'paid')->sum('grand_total') / $totalRevenue) * 100), 2) : 0,
            'operatingCashFlow' => $netProfit,
            'liquidityRatio' => '1.5',
            'debtRatio' => '32',
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'generated_date' => now()->format('d M Y, h:i A'),
            'company_name' => config('app.name', 'SmartERP'),
        ];

        $pdf = Pdf::loadView('reports.pdf.financial_report', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('financial_report_' . date('Y-m-d') . '.pdf');
    }
}
