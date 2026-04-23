<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    public function salesPrediction()
    {
        // 1️⃣ Sales data fetch
        $sales = DB::table('sales')
            ->select('sale_date', 'grand_total')
            ->orderBy('sale_date')
            ->get()
            ->toJson();

        // 2️⃣ Python AI call
        $command = 'py -3 ai/sales_prediction.py ' . escapeshellarg($sales);
        $output = shell_exec($command);

        // 3️⃣ Decode AI result
        $prediction = json_decode($output, true);

        // Safety fallback
        if (!$prediction) {
            $prediction = [
                'next_30_days_total' => 0,
                'daily_prediction_avg' => 0
            ];
        }

        return view('ai.sales_prediction', compact('prediction'));
    }
}
