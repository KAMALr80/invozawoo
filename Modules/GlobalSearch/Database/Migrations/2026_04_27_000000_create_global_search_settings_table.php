<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalSearchSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('global_search_settings')) {
            Schema::create('global_search_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('business_id')->unique();

                $table->boolean('allow_customer')->default(1);
                $table->boolean('allow_supplier')->default(1);
                $table->boolean('allow_invoice')->default(1);
                $table->boolean('allow_purchase_order')->default(1);
                $table->boolean('allow_vendor_bill')->default(1);
                $table->boolean('allow_credit_memo')->default(1);
                $table->boolean('allow_vendor_credit_memo')->default(1);
                $table->boolean('allow_expense')->default(1);
                $table->boolean('allow_followup')->default(1);
                $table->boolean('allow_product')->default(1);
                $table->boolean('allow_menu')->default(1);
                $table->boolean('allow_report')->default(1);

                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('global_search_settings');
    }
}