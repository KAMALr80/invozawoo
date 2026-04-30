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
        // Changing to string to avoid ENUM truncation issues and allow for new statuses
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_status', 30)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Revert to enum if needed, though string is safer
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->change();
        });
    }
};
