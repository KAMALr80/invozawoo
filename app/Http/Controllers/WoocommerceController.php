<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WoocommerceSetting;
use App\Models\Product;
use App\Services\WoocommerceService;

class WoocommerceController extends Controller
{
    public function index()
    {
        $settings = WoocommerceSetting::getSettings();
        $stats = $settings->last_sync_stats ?? [];
        $logs = \App\Models\WoocommerceSyncLog::latest()->take(10)->get();
        
        return view('woocommerce.dashboard', compact('settings', 'stats', 'logs'));
    }

    public function settings()
    {
        $settings = WoocommerceSetting::getSettings();
        return view('woocommerce.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_url' => 'nullable|url',
            'consumer_key' => 'nullable|string',
            'consumer_secret' => 'nullable|string',
        ]);

        $settings = WoocommerceSetting::getSettings();
        $settings->update($request->all());

        return redirect()->back()->with('success', 'WooCommerce settings updated successfully!');
    }

    public function syncProducts(Request $request)
    {
        $service = new WoocommerceService();
        if (!$service->checkConnection()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Could not connect to WooCommerce.']);
            }
            return redirect()->back()->with('error', 'Could not connect to WooCommerce. Please check API keys.');
        }

        // Get total count for progress tracking if AJAX
        $totalProducts = Product::where('is_active', true)->count();
        if ($totalProducts == 0) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'No active products to sync.', 'count' => 0]);
            }
            return redirect()->back()->with('info', 'No active products to sync.');
        }

        $count = 0;
        $failedCount = 0;
        
        // Use chunking to process in batches
        Product::where('is_active', true)->chunk(50, function($products) use ($service, &$count, &$failedCount) {
            $result = $service->syncBatchProducts($products);
            if (is_array($result) && $result['success']) {
                $count += ($result['created'] + $result['updated']);
                // Assuming result could track individual failures in the future
            } else {
                $failedCount += $products->count();
            }
        });

        // Save detailed log
        \App\Models\WoocommerceSyncLog::create([
            'operation_type' => 'Full Sync',
            'items_total' => $totalProducts,
            'items_success' => $count,
            'items_failed' => $failedCount,
            'status' => ($count == $totalProducts) ? 'Completed' : (($count > 0) ? 'Partial' : 'Failed'),
            'details' => ['triggered_by' => auth()->user()->name]
        ]);

        $settings = WoocommerceSetting::getSettings();
        $stats = $settings->last_sync_stats ?? [];
        $stats['products'] = [
            'last_sync' => now()->toDateTimeString(),
            'count' => $count
        ];
        $settings->update(['last_sync_stats' => $stats]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => "Successfully synced {$count} products to WooCommerce!",
                'count' => $count
            ]);
        }

        return redirect()->back()->with('success', "Successfully synced {$count} products to WooCommerce!");
    }

    public function testConnection(Request $request)
    {
        $service = new WoocommerceService($request->app_url, $request->consumer_key, $request->consumer_secret);
        
        if ($service->verifyConnection()) {
            return response()->json(['success' => true, 'message' => 'Successfully connected to WooCommerce!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Failed to connect. Please check your credentials and URL.']);
    }

    public function products()
    {
        $products = Product::where('is_active', true)->paginate(15);
        $settings = WoocommerceSetting::getSettings();
        return view('woocommerce.products', compact('products', 'settings'));
    }

    public function syncSingleProduct($id)
    {
        $product = Product::findOrFail($id);
        $service = new WoocommerceService();
        
        if (!$service->checkConnection()) {
            return response()->json(['success' => false, 'message' => 'Neural Bridge offline. Check API keys.']);
        }

        $result = $service->syncProduct($product);

        if ($result && isset($result['success']) && $result['success']) {
            // Log this specific action
            \App\Models\WoocommerceSyncLog::create([
                'operation_type' => 'Single Sync',
                'items_total' => 1,
                'items_success' => 1,
                'items_failed' => 0,
                'status' => 'Completed',
                'details' => ['product_id' => $product->id, 'product_name' => $product->name]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Pulse Sync Successful: {$product->name} is live on WooCommerce."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Handshake Failed for {$product->name}. Check system logs."
        ]);
    }
}
