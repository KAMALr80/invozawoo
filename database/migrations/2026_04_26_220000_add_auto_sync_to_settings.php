<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('woocommerce_settings', function (Blueprint $row) {
            if (!Schema::hasColumn('woocommerce_settings', 'is_sync_enabled')) {
                $row->boolean('is_sync_enabled')->default(false)->after('consumer_secret');
            }
        });
    }

    public function down(): void
    {
        Schema::table('woocommerce_settings', function (Blueprint $row) {
            $row->dropColumn('is_sync_enabled');
        });
    }
};
