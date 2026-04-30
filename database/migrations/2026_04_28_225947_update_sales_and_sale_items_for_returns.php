<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('refunded_amount', 15, 2)->default(0)->after('paid_amount');
            // Update payment_status to include refund statuses if needed, 
            // but since it's an ENUM in many cases, we might need a separate status or just handle it in logic.
            // Let's assume we can add to the existing enum if it's string based or just use constants.
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('returned_quantity', 15, 2)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('refunded_amount');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('returned_quantity');
        });
    }
};
