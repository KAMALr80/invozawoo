<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WoocommerceSetting;
use App\Models\Product;
use App\Models\WoocommerceSyncLog;
use App\Services\WoocommerceService;

class WoocommerceController extends Controller
{
    public function index()
    {
        $settings = WoocommerceSetting::getSettings();
        $logs = WoocommerceSyncLog::latest()->take(10)->get();
        
        // Calculate status counts
        $pendingProducts = Product::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('synced_at')
                  ->orWhereRaw('updated_at > synced_at');
            })->count();

        return view('woocommerce.dashboard', compact('settings', 'logs', 'pendingProducts'));
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
        $syncType = $request->input('type', 'smart'); // default to smart sync
        
        $connection = $service->checkConnection(true);
        if (!$connection['success']) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $connection['message']]);
            }
            return redirect()->back()->with('error', $connection['message']);
        }

        $result = $service->syncBatchProducts(null, $syncType);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', "Successfully synced {$result['success_count']} products!");
        }

        return redirect()->back()->with('error', $result['message'] ?? 'Synchronization failed');
    }

    public function syncOrders(Request $request)
    {
        $service = new WoocommerceService();
        $connection = $service->checkConnection();
        
        if (!$connection['success']) {
            return response()->json($connection);
        }

        $result = $service->syncOrders();
        
        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', "Successfully synced {$result['synced']} orders!");
    }

    public function testConnection(Request $request)
    {
        // This logic is now inside the service
        $service = new WoocommerceService();
        $result = $service->checkConnection(true);
        
        return response()->json($result);
    }

    public function products()
    {
        $products = Product::where('is_active', true)->paginate(15);
        $settings = WoocommerceSetting::getSettings();
        return view('woocommerce.products', compact('products', 'settings'));
    }

    public function syncSingleProduct($id)
    {
        $service = new WoocommerceService();
        $result = $service->syncBatchProducts([$id], 'all');

        if ($result['success'] && $result['success_count'] > 0) {
            return response()->json([
                'success' => true,
                'message' => "Successfully synced product to WooCommerce."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['errors'][0] ?? ($result['message'] ?? "Sync failed")
        ]);
    }
}
