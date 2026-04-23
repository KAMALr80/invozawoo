<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateProductMrpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if products table exists and has data
        if (DB::table('products')->count() > 0) {

            // Option 1: Set MRP = Price + 10-15% margin
            $products = Product::all();

            foreach ($products as $product) {
                // Set MRP 15% higher than price
                $mrp = round($product->price * 1.15, 2);
                $product->mrp = $mrp;
                $product->save();
            }

            $this->command->info('✅ MRP values updated for ' . $products->count() . ' products!');

        } else {
            $this->command->warn('⚠️ No products found to update MRP.');
        }

        // Alternative: Direct SQL update (faster for large datasets)
        // DB::statement('UPDATE products SET mrp = price * 1.15 WHERE mrp IS NULL OR mrp = 0');
    }
}
