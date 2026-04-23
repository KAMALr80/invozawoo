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
        
        return view('woocommerce.dashboard', compact('settings', 'stats'));
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
        // Use chunking to process in batches
        Product::where('is_active', true)->chunk(50, function($products) use ($service, &$count) {
            $service->syncBatchProducts($products);
            $count += $products->count();
        });

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
}
