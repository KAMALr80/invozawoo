<?php

namespace Database\Migrations;

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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'woocommerce_product_id')) {
                $table->unsignedBigInteger('woocommerce_product_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('products', 'woocommerce_media_id')) {
                $table->unsignedBigInteger('woocommerce_media_id')->nullable()->after('woocommerce_product_id');
            }
            if (!Schema::hasColumn('products', 'woocommerce_sync_disabled')) {
                $table->boolean('woocommerce_sync_disabled')->default(false)->after('woocommerce_media_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'woocommerce_customer_id')) {
                $table->unsignedBigInteger('woocommerce_customer_id')->nullable()->after('id')->index();
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'woocommerce_order_id')) {
                $table->unsignedBigInteger('woocommerce_order_id')->nullable()->after('id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['woocommerce_product_id', 'woocommerce_media_id', 'woocommerce_sync_disabled']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('woocommerce_customer_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('woocommerce_order_id');
        });
    }
};
