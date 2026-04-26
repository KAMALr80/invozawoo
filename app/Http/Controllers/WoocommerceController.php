<?php

namespace App\Http\Controllers;

use App\Utils\WoocommerceUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WoocommerceController extends Controller
{
    protected $woocommerceUtil;

    public function __construct(WoocommerceUtil $woocommerceUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
    }

    /**
     * Display WooCommerce Settings & Sync Page
     */
    public function index()
    {
        $settings = DB::table('woocommerce_settings')->first();
        $products = \App\Models\Product::orderBy('name')->get();
        return view('woocommerce.index', compact('settings', 'products'));
    }

    /**
     * Manual Sync Single Product
     */
    public function syncSingleProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        try {
            $result = $this->woocommerceUtil->syncProducts($request->product_id);
            
            if (!empty($result['errors'])) {
                return redirect()->back()->with('error', 'Sync completed with errors: ' . implode(', ', $result['errors']));
            }

            return redirect()->back()->with('success', 'Product synced successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Update WooCommerce API Settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'store_url' => 'required|url',
            'consumer_key' => 'required',
            'consumer_secret' => 'required',
        ]);

        $data = [
            'store_url' => $request->store_url,
            'consumer_key' => $request->consumer_key,
            'consumer_secret' => $request->consumer_secret,
            'updated_at' => now(),
        ];

        $settings = DB::table('woocommerce_settings')->first();

        if ($settings) {
            DB::table('woocommerce_settings')->where('id', $settings->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('woocommerce_settings')->insert($data);
        }

        return redirect()->back()->with('success', 'WooCommerce settings updated successfully.');
    }

    /**
     * Manual Sync Products
     */
    public function syncProducts()
    {
        try {
            $result = $this->woocommerceUtil->syncProducts();
            $msg = count($result['synced_ids']) . ' products synced successfully.';
            
            if (!empty($result['errors'])) {
                return redirect()->back()->with('warning', $msg . ' But some failed: ' . implode('; ', array_slice($result['errors'], 0, 3)) . '...');
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
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
        $response = $client->get('system_status');
        
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
            'total_products' => \App\Models\Product::count(),
            'total_orders' => \App\Models\Sale::count(),
            'total_customers' => \App\Models\Customer::count(),
            'total_revenue' => number_format(\App\Models\Sale::sum('grand_total'), 2),
            'products_synced' => \App\Models\Product::whereNotNull('woocommerce_product_id')->count(),
            'orders_synced' => \App\Models\Sale::whereNotNull('woocommerce_order_id')->count(),
            'customers_synced' => \App\Models\Customer::whereNotNull('woocommerce_customer_id')->count(),
            'last_sync' => optional($settings)->updated_at?->diffForHumans()
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
