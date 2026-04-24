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
        Schema::create('woocommerce_sync_logs', function (Blueprint $blade) {
            $blade->id();
            $blade->string('operation_type'); // Full Sync, Single Product, Stock Update
            $blade->integer('items_total')->default(0);
            $blade->integer('items_success')->default(0);
            $blade->integer('items_failed')->default(0);
            $blade->string('status'); // Completed, Partial, Failed
            $blade->json('details')->nullable();
            $blade->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_sync_logs');
    }
};
