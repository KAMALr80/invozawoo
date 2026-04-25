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
        if (!Schema::hasTable('woocommerce_settings')) {
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
        } else {
            // Table exists but might be missing columns from a failed migration
            Schema::table('woocommerce_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('woocommerce_settings', 'store_url')) {
                    $table->string('store_url')->after('id');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'consumer_key')) {
                    $table->string('consumer_key')->after('store_url');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'consumer_secret')) {
                    $table->string('consumer_secret')->after('consumer_key');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'is_sync_enabled')) {
                    $table->boolean('is_sync_enabled')->default(true)->after('consumer_secret');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'sync_products')) {
                    $table->boolean('sync_products')->default(true)->after('is_sync_enabled');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'sync_orders')) {
                    $table->boolean('sync_orders')->default(true)->after('sync_products');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'sync_customers')) {
                    $table->boolean('sync_customers')->default(true)->after('sync_orders');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'webhook_secret')) {
                    $table->string('webhook_secret')->nullable()->after('sync_customers');
                }
                if (!Schema::hasColumn('woocommerce_settings', 'last_sync_at')) {
                    $table->timestamp('last_sync_at')->nullable()->after('webhook_secret');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_settings');
    }
};
