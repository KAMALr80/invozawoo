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
        Schema::create('woocommerce_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_url');
            $table->string('consumer_key');
            $table->string('consumer_secret');
            $table->boolean('is_sync_enabled')->default(true);
            $table->boolean('sync_products')->default(true);
            $table->boolean('sync_orders')->default(true);
            $table->boolean('sync_customers')->default(true);
            $table->string('webhook_secret')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_settings');
    }
};
