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
        Schema::create('credit_memos', function (Blueprint $table) {
            $table->id();
            $table->string('cm_number')->unique();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers');
            $table->enum('type', ['full', 'partial', 'adjustment'])->default('partial');
            $table->enum('status', ['draft', 'approved', 'refunded', 'cancelled'])->default('draft');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->boolean('restock')->default(true);
            $table->enum('refund_method', ['wallet', 'cash', 'bank', 'original_method'])->default('wallet');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('sale_id');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_memos');
    }
};
