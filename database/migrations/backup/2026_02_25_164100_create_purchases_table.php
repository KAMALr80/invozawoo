<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_number')->unique();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('tax', 5, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->date('purchase_date');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->string('supplier_email')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};