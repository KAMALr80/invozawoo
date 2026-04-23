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
            $table->string('app_url')->nullable();
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            
            // Sync Settings
            $table->boolean('auto_sync')->default(false);
            $table->string('default_tax_class')->nullable();
            $table->string('sync_price_type')->default('including_tax'); // including_tax, excluding_tax
            
            // JSON fields for flexible configuration
            $table->json('product_fields')->nullable(); // fields to sync: name, price, qty, etc.
            $table->json('last_sync_stats')->nullable(); // last sync times and counts
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('woocommerce_settings');
    }
};
