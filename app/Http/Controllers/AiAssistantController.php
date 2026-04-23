<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AiAssistantController extends Controller
{
    public function ask(Request $request)
    {
        $q = strtolower(trim($request->question));

        /* =======================
           CONTEXT MEMORY
        ======================= */
        session(['last_question' => $q]);

        /* =======================
           HELPER: PRODUCT FINDER
        ======================= */
        $findProduct = function ($q) {
            $words = explode(' ', $q);

            $product = Product::where(function ($query) use ($words) {
                foreach ($words as $w) {
                    if (strlen($w) > 2) {
                        $query->orWhere('name', 'LIKE', "%$w%");
                    }
                }
            })->first();

            if ($product) {
                session(['last_product' => $product->id]);
                return $product;
            }

            return session('last_product')
                ? Product::find(session('last_product'))
                : null;
        };

        /* =======================
           BASIC / HUMAN
        ======================= */

        if (str_contains($q, 'who are you')) {
            return response()->json([
                'answer' =>
                    "Main aapka ERP AI Assistant hoon 🤖. Main sales, stock, employees aur business analysis me madad karta hoon."
            ]);
        }

        if (str_contains($q, 'hindi')) {
            return response()->json([
                'answer' =>
                    "Haan 😊, main Hindi aur English dono me baat kar sakta hoon."
            ]);
        }

        /* =======================
           SALES
        ======================= */

        if (str_contains($q, 'today') && str_contains($q, 'sale')) {
            $amount = Sale::whereDate('sale_date', today())->sum('grand_total');
            return response()->json([
                'answer' =>
                    "Aaj ki total sales ₹" . number_format($amount, 2) . " hai."
            ]);
        }

        if (str_contains($q, 'last 7') || str_contains($q, 'week')) {
            $sum = Sale::whereDate('sale_date', '>=', Carbon::now()->subDays(6))
                ->sum('grand_total');

            return response()->json([
                'answer' =>
                    "Last 7 din ki total sales ₹" . number_format($sum, 2) . " hai."
            ]);
        }

        /* =======================
           AI PREDICTION (LOCAL PYTHON)
        ======================= */

        if (str_contains($q, 'predict') || str_contains($q, 'next month')) {

            $sales = Sale::select('sale_date', 'grand_total')
                ->orderBy('sale_date')
                ->get()
                ->toJson();

            $cmd = 'py -3 ai/sales_prediction.py ' . escapeshellarg($sales);
            $out = shell_exec($cmd);
            $pred = json_decode($out, true);

            return response()->json([
                'answer' =>
                    "AI ke hisaab se next month expected sales approx ₹" .
                    number_format($pred['next_30_days_total'] ?? 0, 2) . " ho sakti hai."
            ]);
        }

        /* =======================
           PRODUCT PRICE
        ======================= */

        if (str_contains($q, 'price') || str_contains($q, 'rate')) {
            $product = $findProduct($q);

            if ($product) {
                return response()->json([
                    'answer' =>
                        "{$product->name} ka price ₹" .
                        number_format($product->price, 2) . " hai."
                ]);
            }
        }

        /* =======================
           PRODUCT STOCK
        ======================= */

        if (str_contains($q, 'stock') || str_contains($q, 'quantity')) {
            $product = $findProduct($q);

            if ($product) {
                return response()->json([
                    'answer' =>
                        "{$product->name} ka current stock {$product->quantity} units hai."
                ]);
            }
        }

        /* =======================
           LOW STOCK
        ======================= */

        if (str_contains($q, 'low stock')) {
            $products = Product::where('quantity', '<=', 5)->pluck('name')->toArray();

            if (!$products) {
                return response()->json([
                    'answer' => "Sab products ka stock safe level par hai 👍"
                ]);
            }

            return response()->json([
                'answer' =>
                    "Low stock products: " . implode(', ', $products)
            ]);
        }

        /* =======================
           EMPLOYEES
        ======================= */

        if (str_contains($q, 'employee')) {
            $count = Employee::count();
            return response()->json([
                'answer' =>
                    "Abhi total {$count} employees kaam kar rahe hain."
            ]);
        }

        /* =======================
           FALLBACK
        ======================= */

        return response()->json([
            'answer' =>
                "Main sales, stock, price, employees aur basic business analysis me madad kar sakta hoon 🙂"
        ]);
    }
}
