<?php

namespace Modules\GlobalSearch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Contact;
use App\Product;
use App\Transaction;

class SearchController  extends Controller
{
    public function globalSearch(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([]);
        }

        $term = trim($request->get('term', ''));
        $category = trim($request->get('category', 'all'));

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $business_id = $request->session()->get('user.business_id');

        $search_settings = DB::table('global_search_settings')
            ->where('business_id', $business_id)
            ->first();

        if (empty($search_settings)) {
            $search_settings = (object) [
                'allow_customer' => 1,
                'allow_supplier' => 1,
                'allow_invoice' => 1,
                'allow_purchase_order' => 1,
                'allow_vendor_bill' => 1,
                'allow_credit_memo' => 1,
                'allow_vendor_credit_memo' => 1,
                'allow_expense' => 1,
                'allow_followup' => 1,
                'allow_product' => 1,
                'allow_menu' => 1,
                'allow_report' => 1,
            ];
        }

        $results = [];

        /*
        |--------------------------------------------------------------------------
        | MENU LINKS
        |--------------------------------------------------------------------------
        */
        $menu_links = [
            ['title' => 'Dashboard', 'url' => route('home')],
            ['title' => 'Products', 'url' => url('/products')],
            ['title' => 'Contacts', 'url' => action('ContactController@index', ['type' => 'customer'])],
            ['title' => 'Customers', 'url' => url('/contacts?type=customer')],
            ['title' => 'Suppliers', 'url' => url('/contacts?type=supplier')],
            ['title' => 'Sales', 'url' => url('/sells')],
            ['title' => 'POS', 'url' => action('SellPosController@create')],
            ['title' => 'Purchase Orders', 'url' => url('/purchase-order')],
            ['title' => 'Purchases / Vendor Bills', 'url' => url('/purchases')],
            ['title' => 'Expenses', 'url' => url('/expenses')],
            ['title' => 'Followups', 'url' => url('/smart-crm/follow-up')],
            ['title' => 'Sell Return / Credit Memo', 'url' => url('/sell-return')],
            ['title' => 'Purchase Return / Vendor Credit Memo', 'url' => url('/purchase-return')],
            ['title' => 'Calendar', 'url' => route('calendar')],
            ['title' => 'Payment Accounts', 'url' => url('/account/account')],
        ];

        if (($category == 'all' || $category == 'menu') && !empty($search_settings->allow_menu)) {
            foreach ($menu_links as $menu) {
                if (stripos($menu['title'], $term) !== false) {
                    $results[] = [
                        'type' => 'Menu',
                        'title' => $menu['title'],
                        'subtitle' => 'Quick menu link',
                        'url' => $menu['url'],
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | REPORT LINKS
        |--------------------------------------------------------------------------
        */
        $report_links = [
            ['title' => 'Profit / Loss Report', 'url' => url('/reports/profit-loss')],
            ['title' => 'Items Report', 'url' => url('/reports/items-report')],
            ['title' => 'Register Report', 'url' => url('/reports/register-report')],
            ['title' => 'Sales Representative Report', 'url' => url('/reports/sales-representative-report')],
            ['title' => 'Expense Report', 'url' => url('/reports/expense-report')],
            ['title' => 'Stock Report', 'url' => url('/reports/stock-report')],
            ['title' => 'Trending Products Report', 'url' => url('/reports/trending-products')],
            ['title' => 'Purchase & Sale Report', 'url' => url('/reports/purchase-sale')],
            ['title' => 'Tax Report', 'url' => url('/reports/tax-report')],
            ['title' => 'Customer Group Report', 'url' => url('/reports/customer-group')],
            ['title' => 'Service Staff Report', 'url' => url('/reports/service-staff-report')],
            ['title' => 'Stock Adjustment Report', 'url' => url('/reports/stock-adjustment-report')],
            ['title' => 'Stock Expiry Report', 'url' => url('/reports/stock-expiry-report')],
            ['title' => 'Lot Report', 'url' => url('/reports/lot-report')],
            ['title' => 'Purchase Payment Report', 'url' => url('/reports/purchase-payment-report')],
            ['title' => 'Sell Payment Report', 'url' => url('/reports/sell-payment-report')],
            ['title' => 'State Tax Report', 'url' => url('/reports/state-tax-report')],
            ['title' => 'Back Order Report', 'url' => url('/reports/back-order')],
            ['title' => 'Understock Report', 'url' => url('/reports/understock')],
            ['title' => 'Overstock Report', 'url' => url('/reports/overstock')],
            ['title' => 'Velocity Report', 'url' => url('/reports/velocity')],
            ['title' => 'SKU Performance Report', 'url' => url('/reports/sku-performance')],
            ['title' => 'Price Update Report', 'url' => url('/reports/price-update')],
            ['title' => 'AR Report', 'url' => url('/reports/AR-report')],
            ['title' => 'End Of Week Report', 'url' => url('/reports/weekend-report')],
            ['title' => 'Collected Payments Report', 'url' => url('/reports/collected-payment-report')],
            ['title' => 'New Balance Report', 'url' => url('/reports/BalanceReport')],
            ['title' => 'Balance Report', 'url' => url('/reports/balance-report')],
            ['title' => 'Payables and receivables Report', 'url' => url('/reports/payables-receivables-report')],
            ['title' => 'Late Payment Report', 'url' => url('/reports/late-payment-report')],
            ['title' => 'Product Analytics', 'url' => url('/Analytics/Product')],
            ['title' => 'Category Wise Sale Report', 'url' => url('/reports/category-wise-sale-report')],
            ['title' => 'Brand Wise Sale Report', 'url' => url('/reports/brand-wise-sale-report')],
            ['title' => 'Brand Commission Tier Discount Report', 'url' => url('/reports/brand-commission-discount-report')],
            ['title' => 'Product Wise Sale Report', 'url' => url('/reports/product-wise-sale-report')],
            ['title' => 'Sales Tree Report', 'url' => url('/reports/sales-tree-report')],
            ['title' => 'Product Selling Report', 'url' => url('/reports/product-selling-report')],
            ['title' => 'Export Invoices', 'url' => url('/sells/pos/export_invoice')],
            ['title' => 'E-check Generator', 'url' => url('/reports/echeck-generator')],
            ['title' => 'Under Stocked Product Report', 'url' => url('/reports/understock')],
            ['title' => 'Over Stocked Product Report', 'url' => url('/reports/overstock')],
            ['title' => 'Item Inventory', 'url' => url('/reports/item-inventory')],
            ['title' => 'Product Sell Report', 'url' => url('/reports/product-sell-report')],
            ['title' => 'Product Purchase Report', 'url' => url('/reports/product-purchase-report')],
            ['title' => 'Out Of Stock Report', 'url' => url('/reports/out-of-stock-report')],
            ['title' => 'Daily Items Report', 'url' => url('/reports/daily-items-report')],
            ['title' => 'Customers & Suppliers Reports', 'url' => url('/reports/customer-supplier')],
            ['title' => 'Customer Groups Report', 'url' => url('/reports/customer-group')],
            ['title' => 'Stale Customers Reports', 'url' => url('/reports/stale-customer')],
            ['title' => 'Deleted Product Report', 'url' => url('/reports/deleted-product-report')],
            ['title' => 'Deleted Invoices Report', 'url' => url('/reports/deleted-invoices-report')],
            ['title' => 'Deleted Transaction Payments Report', 'url' => url('/reports/deleted-transaction-payments-report')],
            ['title' => 'Deleted CreditMemo Report', 'url' => url('/reports/deleted-creditmemo-report')],
            ['title' => 'Deleted Vendor Credit Memo Report', 'url' => url('/reports/deleted-vendorcreditmemo-report')],
            ['title' => 'Deleted Vendor Bills Report', 'url' => url('/reports/deleted-purchases-report')],
            ['title' => 'Deleted Purchase Orders Report', 'url' => url('/reports/deleted-purchases-order-report')],
            ['title' => 'Deleted Stock Adjustments Report', 'url' => url('/reports/deleted-stock-adjustments-report')],
        ];

        if (($category == 'all' || $category == 'report') && !empty($search_settings->allow_report)) {
            foreach ($report_links as $report) {
                if (stripos($report['title'], $term) !== false) {
                    $results[] = [
                        'type' => 'Report',
                        'title' => $report['title'],
                        'subtitle' => 'Open report page',
                        'url' => $report['url'],
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'product') && !empty($search_settings->allow_product)) {
            $products = Product::where('business_id', $business_id)
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('sku', 'like', '%' . $term . '%')
                        ->orWhere('type', 'like', '%' . $term . '%')
                        ->orWhere('aisle', 'like', '%' . $term . '%')
                        ->orWhere('rack', 'like', '%' . $term . '%')
                        ->orWhere('shelf', 'like', '%' . $term . '%')
                        ->orWhere('bin', 'like', '%' . $term . '%');
                })
                ->limit(10)
                ->get();

            foreach ($products as $product) {
                $location_parts = [];

                if (!empty($product->aisle)) {
                    $location_parts[] = 'Location: ' . $product->aisle;
                }
                if (!empty($product->rack)) {
                    $location_parts[] = 'Rack: ' . $product->rack;
                }
                if (!empty($product->shelf)) {
                    $location_parts[] = 'Row: ' . $product->shelf;
                }
                if (!empty($product->bin)) {
                    $location_parts[] = 'Position: ' . $product->bin;
                }

                $subtitle_parts = [];

                if (!empty($product->type)) {
                    $subtitle_parts[] = 'Type: ' . ucfirst($product->type);
                }
                if (!empty($product->sku)) {
                    $subtitle_parts[] = 'SKU: ' . $product->sku;
                }
                if (!empty($location_parts)) {
                    $subtitle_parts[] = implode(' | ', $location_parts);
                }

                $results[] = [
                    'type' => 'Product',
                    'title' => $product->name,
                    'subtitle' => !empty($subtitle_parts) ? implode(' | ', $subtitle_parts) : 'Product',
                    'url' => url('/products/' . $product->id . '/edit'),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMERS
        |--------------------------------------------------------------------------
        */
        if (
            ($category == 'all' && !empty($search_settings->allow_customer)) ||
            ($category == 'customer' && !empty($search_settings->allow_customer))
        ) {
            $customers = Contact::where('business_id', $business_id)
                ->whereIn('type', ['customer', 'both'])
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('supplier_business_name', 'like', '%' . $term . '%')
                        ->orWhere('mobile', 'like', '%' . $term . '%')
                        ->orWhere('contact_id', 'like', '%' . $term . '%')
                        ->orWhere('email', 'like', '%' . $term . '%');
                })
                ->limit(12)
                ->get();

            foreach ($customers as $customer) {
                $display_name = !empty($customer->supplier_business_name)
                    ? $customer->supplier_business_name
                    : $customer->name;

                $subtitle_parts = [];

                if (!empty($customer->contact_id)) {
                    $subtitle_parts[] = 'Contact ID: ' . $customer->contact_id;
                }
                if (!empty($customer->mobile)) {
                    $subtitle_parts[] = 'Mobile: ' . $customer->mobile;
                }
                if (!empty($customer->email)) {
                    $subtitle_parts[] = 'Email: ' . $customer->email;
                }

                $results[] = [
                    'type' => 'Customer',
                    'title' => $display_name,
                    'subtitle' => !empty($subtitle_parts) ? implode(' | ', $subtitle_parts) : 'Customer record',
                    'url' => url('/contacts/' . $customer->id),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUPPLIERS
        |--------------------------------------------------------------------------
        */
        if (
            ($category == 'all' && !empty($search_settings->allow_supplier)) ||
            ($category == 'supplier' && !empty($search_settings->allow_supplier))
        ) {
            $suppliers = Contact::where('business_id', $business_id)
                ->whereIn('type', ['supplier', 'both'])
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('supplier_business_name', 'like', '%' . $term . '%')
                        ->orWhere('mobile', 'like', '%' . $term . '%')
                        ->orWhere('contact_id', 'like', '%' . $term . '%')
                        ->orWhere('email', 'like', '%' . $term . '%');
                })
                ->limit(12)
                ->get();

            foreach ($suppliers as $supplier) {
                $display_name = !empty($supplier->supplier_business_name)
                    ? $supplier->supplier_business_name
                    : $supplier->name;

                $subtitle_parts = [];

                if (!empty($supplier->contact_id)) {
                    $subtitle_parts[] = 'Contact ID: ' . $supplier->contact_id;
                }
                if (!empty($supplier->mobile)) {
                    $subtitle_parts[] = 'Mobile: ' . $supplier->mobile;
                }
                if (!empty($supplier->email)) {
                    $subtitle_parts[] = 'Email: ' . $supplier->email;
                }

                $results[] = [
                    'type' => 'Supplier',
                    'title' => $display_name,
                    'subtitle' => !empty($subtitle_parts) ? implode(' | ', $subtitle_parts) : 'Supplier record',
                    'url' => url('/contacts/' . $supplier->id),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS
        |--------------------------------------------------------------------------
        */
        if ($category == 'all' || $category == 'account') {
            $accounts = DB::table('accounts')
                ->where('business_id', $business_id)
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('account_number', 'like', '%' . $term . '%')
                        ->orWhere('note', 'like', '%' . $term . '%');
                })
                ->limit(12)
                ->get();

            foreach ($accounts as $account) {
                $subtitle_parts = [];

                if (!empty($account->account_number)) {
                    $subtitle_parts[] = 'Account No: ' . $account->account_number;
                }

                if (!empty($account->note)) {
                    $subtitle_parts[] = $account->note;
                }

                $results[] = [
                    'type' => 'Account',
                    'title' => $account->name,
                    'subtitle' => !empty($subtitle_parts) ? implode(' | ', $subtitle_parts) : 'Account record',
                    'url' => url('/account/account'),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INVOICE
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'invoice') && !empty($search_settings->allow_invoice)) {
            $invoices = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where(function ($query) use ($term) {
                    $query->where('invoice_no', 'like', '%' . $term . '%')
                        ->orWhere('ref_no', 'like', '%' . $term . '%');
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($invoices as $invoice) {
                $results[] = [
                    'type' => 'Invoice',
                    'title' => !empty($invoice->invoice_no) ? $invoice->invoice_no : 'Invoice #' . $invoice->id,
                    'subtitle' => 'Status: ' . ucfirst($invoice->status),
                    'url' => url('/pos/' . $invoice->id . '/edit'),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDER
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'purchase_order') && !empty($search_settings->allow_purchase_order)) {
            $purchase_orders = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase_order')
                ->where(function ($query) use ($term) {
                    $query->where('ref_no', 'like', '%' . $term . '%')
                        ->orWhere('invoice_no', 'like', '%' . $term . '%');
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($purchase_orders as $po) {
                $results[] = [
                    'type' => 'Purchase Order',
                    'title' => !empty($po->ref_no) ? $po->ref_no : (!empty($po->invoice_no) ? $po->invoice_no : 'PO #' . $po->id),
                    'subtitle' => 'Status: ' . ucfirst($po->status),
                    'url' => url('/purchase-order/' . $po->id),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VENDOR BILL
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'vendor_bill') && !empty($search_settings->allow_vendor_bill)) {
            $vendor_bills = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase')
                ->where(function ($query) use ($term) {
                    $query->where('ref_no', 'like', '%' . $term . '%')
                        ->orWhere('invoice_no', 'like', '%' . $term . '%');
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($vendor_bills as $bill) {
                $results[] = [
                    'type' => 'Vendor Bill',
                    'title' => !empty($bill->ref_no) ? $bill->ref_no : (!empty($bill->invoice_no) ? $bill->invoice_no : 'Bill #' . $bill->id),
                    'subtitle' => 'Status: ' . ucfirst($bill->status),
                    'url' => url('/purchases/' . $bill->id . '/edit'),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREDIT MEMO
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'credit_memo') && !empty($search_settings->allow_credit_memo)) {
            $credit_memos = Transaction::where('business_id', $business_id)
                ->where('type', 'sell_return')
                ->where(function ($query) use ($term) {
                    $query->where('invoice_no', 'like', '%' . $term . '%')
                        ->orWhere('ref_no', 'like', '%' . $term . '%');
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($credit_memos as $memo) {
                $results[] = [
                    'type' => 'Credit Memo',
                    'title' => !empty($memo->invoice_no) ? $memo->invoice_no : (!empty($memo->ref_no) ? $memo->ref_no : 'Credit Memo #' . $memo->id),
                    'subtitle' => 'Status: ' . ucfirst($memo->status),
                    'url' => url('/sell-return/add/' . $memo->id),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VENDOR CREDIT MEMO
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'vendor_credit_memo') && !empty($search_settings->allow_vendor_credit_memo)) {
            $vendor_credit_memos = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase_return')
                ->where(function ($query) use ($term) {
                    $query->where('invoice_no', 'like', '%' . $term . '%')
                        ->orWhere('ref_no', 'like', '%' . $term . '%');
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($vendor_credit_memos as $memo) {
                $results[] = [
                    'type' => 'Vendor Credit Memo',
                    'title' => !empty($memo->invoice_no) ? $memo->invoice_no : (!empty($memo->ref_no) ? $memo->ref_no : 'Vendor Credit Memo #' . $memo->id),
                    'subtitle' => 'Status: ' . ucfirst($memo->status),
                    'url' => url('/purchase-return/edit/' . $memo->id),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EXPENSE
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'expense') && !empty($search_settings->allow_expense)) {
            $expenses = Transaction::where('business_id', $business_id)
                ->where('type', 'expense')
                ->where(function ($query) use ($term) {
                    $query->where('ref_no', 'like', '%' . $term . '%')
                        ->orWhere('invoice_no', 'like', '%' . $term . '%')
                        ->orWhere('additional_notes', 'like', '%' . $term . '%');
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($expenses as $expense) {
                $results[] = [
                    'type' => 'Expense',
                    'title' => !empty($expense->ref_no)
                        ? $expense->ref_no
                        : (!empty($expense->invoice_no) ? $expense->invoice_no : 'Expense #' . $expense->id),
                    'subtitle' => !empty($expense->additional_notes) ? $expense->additional_notes : 'Expense entry',
                    'url' => url('/expenses/' . $expense->id . '/edit'),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FOLLOWUP
        |--------------------------------------------------------------------------
        */
        if (($category == 'all' || $category == 'followup') && !empty($search_settings->allow_followup)) {
            try {
                $followups = DB::table('contact_follow_ups')
                    ->leftJoin('contacts', 'contact_follow_ups.contact_id', '=', 'contacts.id')
                    ->where('contacts.business_id', $business_id)
                    ->where(function ($query) use ($term) {
                        $query->where('contact_follow_ups.title', 'like', '%' . $term . '%')
                            ->orWhere('contact_follow_ups.description', 'like', '%' . $term . '%')
                            ->orWhere('contacts.name', 'like', '%' . $term . '%')
                            ->orWhere('contacts.supplier_business_name', 'like', '%' . $term . '%');
                    })
                    ->select(
                        'contact_follow_ups.id',
                        'contact_follow_ups.title',
                        'contact_follow_ups.description',
                        'contact_follow_ups.contact_id',
                        'contacts.name',
                        'contacts.supplier_business_name'
                    )
                    ->limit(10)
                    ->get();

                foreach ($followups as $followup) {
                    $contact_name = !empty($followup->supplier_business_name)
                        ? $followup->supplier_business_name
                        : $followup->name;

                    $results[] = [
                        'type' => 'Followup',
                        'title' => !empty($followup->title) ? $followup->title : 'Followup #' . $followup->id,
                        'subtitle' => 'Contact: ' . $contact_name,
                        'url' => url('/contacts/' . $followup->contact_id),
                    ];
                }
            } catch (\Exception $e) {
                // Ignore followup table error.
            }
        }

        $results = array_slice($results, 0, 50);

        return response()->json($results);
    }

    public function globalSearchConfig(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $settings = DB::table('global_search_settings')
            ->where('business_id', $business_id)
            ->first();

        if (empty($settings)) {
            return response()->json([
                'all' => 1,
                'customer' => 1,
                'supplier' => 1,
                'invoice' => 1,
                'purchase_order' => 1,
                'vendor_bill' => 1,
                'credit_memo' => 1,
                'vendor_credit_memo' => 1,
                'expense' => 1,
                'followup' => 1,
                'product' => 1,
                'menu' => 1,
                'report' => 1,
            ]);
        }

        return response()->json([
            'all' => 1,
            'customer' => !empty($settings->allow_customer) ? 1 : 0,
            'supplier' => !empty($settings->allow_supplier) ? 1 : 0,
            'invoice' => !empty($settings->allow_invoice) ? 1 : 0,
            'purchase_order' => !empty($settings->allow_purchase_order) ? 1 : 0,
            'vendor_bill' => !empty($settings->allow_vendor_bill) ? 1 : 0,
            'credit_memo' => !empty($settings->allow_credit_memo) ? 1 : 0,
            'vendor_credit_memo' => !empty($settings->allow_vendor_credit_memo) ? 1 : 0,
            'expense' => !empty($settings->allow_expense) ? 1 : 0,
            'followup' => !empty($settings->allow_followup) ? 1 : 0,
            'product' => !empty($settings->allow_product) ? 1 : 0,
            'menu' => !empty($settings->allow_menu) ? 1 : 0,
            'report' => !empty($settings->allow_report) ? 1 : 0,
        ]);
    }
     public function globalSearchSettingsIndex()
{
    if (!auth()->check()) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');

    $settings = DB::table('global_search_settings')
        ->where('business_id', $business_id)
        ->first();

    if (empty($settings)) {
        DB::table('global_search_settings')->insert([
            'business_id' => $business_id,
            'allow_customer' => 1,
            'allow_supplier' => 1,
            'allow_invoice' => 1,
            'allow_purchase_order' => 1,
            'allow_vendor_bill' => 1,
            'allow_credit_memo' => 1,
            'allow_vendor_credit_memo' => 1,
            'allow_expense' => 1,
            'allow_followup' => 1,
            'allow_product' => 1,
            'allow_menu' => 1,
            'allow_report' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $settings = DB::table('global_search_settings')
            ->where('business_id', $business_id)
            ->first();
    }

    return view('globalsearch::global_search_settings.index', compact('settings'));
}

public function globalSearchSettingsStore(Request $request)
{
    if (!auth()->check()) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = $request->session()->get('user.business_id');

    DB::table('global_search_settings')->updateOrInsert(
        ['business_id' => $business_id],
        [
            'allow_customer' => $request->has('allow_customer') ? 1 : 0,
            'allow_supplier' => $request->has('allow_supplier') ? 1 : 0,
            'allow_invoice' => $request->has('allow_invoice') ? 1 : 0,
            'allow_purchase_order' => $request->has('allow_purchase_order') ? 1 : 0,
            'allow_vendor_bill' => $request->has('allow_vendor_bill') ? 1 : 0,
            'allow_credit_memo' => $request->has('allow_credit_memo') ? 1 : 0,
            'allow_vendor_credit_memo' => $request->has('allow_vendor_credit_memo') ? 1 : 0,
            'allow_expense' => $request->has('allow_expense') ? 1 : 0,
            'allow_followup' => $request->has('allow_followup') ? 1 : 0,
            'allow_product' => $request->has('allow_product') ? 1 : 0,
            'allow_menu' => $request->has('allow_menu') ? 1 : 0,
            'allow_report' => $request->has('allow_report') ? 1 : 0,
            'updated_at' => now(),
        ]
    );

    return redirect()
        ->back()
        ->with('status', ['success' => 1, 'msg' => 'Global search settings updated successfully']);
}

}