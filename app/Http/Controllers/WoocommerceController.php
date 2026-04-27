<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\WoocommerceUtil;
use App\Models\Product;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WoocommerceController extends Controller
{
    protected $woocommerceUtil;

    public function __construct(WoocommerceUtil $woocommerceUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
    }

    public function index()
    {
        $products = Product::all();
        $settings = DB::table('woocommerce_settings')->first();
        return view('woocommerce.index', compact('products', 'settings'));
    }

    /**
     * Manual Sync Products
     */
    public function syncProducts()
    {
        try {
            $syncedIds = $this->woocommerceUtil->syncProducts();
            return redirect()->back()->with('success', count($syncedIds) . ' products synced successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Single Product Sync (AJAX)
     */
    public function syncSingleProduct(Request $request)
    {
        try {
            $p = Product::findOrFail($request->product_id);
            $res = $this->woocommerceUtil->syncProducts($p->id);
            
            if (!empty($res['errors'])) {
                return response()->json(['success' => false, 'error' => $res['errors'][0]]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Manual Sync Orders
     */
    public function syncOrders()
    {
        try {
            $syncedIds = $this->woocommerceUtil->syncOrders();
            return redirect()->back()->with('success', count($syncedIds) . ' orders synced successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Manual Sync Customers
     */
    public function syncCustomers()
    {
        try {
            $syncedIds = $this->woocommerceUtil->syncCustomers();
            return redirect()->back()->with('success', count($syncedIds) . ' customers synced successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Test WooCommerce connection
     */
    public function testConnection(Request $request)
    {
        try {
            $request->validate([
                'store_url' => 'required|url',
                'consumer_key' => 'required',
                'consumer_secret' => 'required',
            ]);

            $storeUrl = rtrim($request->store_url, '/');
            
            $client = new Client($storeUrl, $request->consumer_key, $request->consumer_secret, [
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 30,
                'verify_ssl' => false
            ]);
            
            // Test API by getting system status
            $client->get('system_status');
            
            return response()->json([
                'success' => true,
                'message' => 'Connection successful! WooCommerce API is accessible.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getSyncStats()
    {
        $settings = DB::table('woocommerce_settings')->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => Product::count(),
                'total_orders' => \App\Models\Sale::count(),
                'total_customers' => \App\Models\Customer::count(),
                'total_revenue' => number_format(\App\Models\Sale::sum('grand_total'), 2),
                'products_synced' => Product::whereNotNull('woocommerce_product_id')->count(),
                'orders_synced' => \App\Models\Sale::whereNotNull('woocommerce_order_id')->count(),
                'customers_synced' => \App\Models\Customer::whereNotNull('woocommerce_customer_id')->count(),
                'last_sync' => $settings && $settings->updated_at ? Carbon::parse($settings->updated_at)->diffForHumans() : 'Never'
            ]
        ]);
    }

    public function toggleAutoSync(Request $request)
    {
        $settings = DB::table('woocommerce_settings')->first();
        
        if ($settings) {
            DB::table('woocommerce_settings')
                ->where('id', $settings->id)
                ->update(['is_sync_enabled' => $request->enabled]);
        }
        
        return response()->json(['success' => true]);
    }
}
