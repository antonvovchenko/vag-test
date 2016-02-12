<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToProductTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('ndc');
            $table->string('product_title');
            $table->string('generic');
            $table->string('pkg_size');
            $table->string('unit_landed');
            $table->string('selling_price');
            $table->integer('tax_id');
            $table->string('barcode');
            $table->string('location');
            $table->string('unit_cost');
            $table->string('retail_markup');
            $table->string('unit_selling');
            $table->string('last_purchase');
            $table->string('last_supplier');
            $table->string('batch_number');
            $table->string('label_def');
            $table->integer('category_id');
            $table->string('balance');
            $table->string('min_qty');
            $table->string('max_qty');
            $table->timestamp('last_sale');
            $table->string('lead_time');
            $table->timestamp('expiry_date');
            $table->string('nhf_subsidiary');
            $table->boolean('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ndc');
            $table->dropColumn('product_title');
            $table->dropColumn('generic');
            $table->dropColumn('pkg_size');
            $table->dropColumn('unit_landed');
            $table->dropColumn('selling_price');
            $table->dropColumn('tax_id');
            $table->dropColumn('barcode');
            $table->dropColumn('location');
            $table->dropColumn('unit_cost');
            $table->dropColumn('retail_markup');
            $table->dropColumn('unit_selling');
            $table->dropColumn('last_purchase');
            $table->dropColumn('last_supplier');
            $table->dropColumn('batch_number');
            $table->dropColumn('label_def');
            $table->dropColumn('category_id');
            $table->dropColumn('balance');
            $table->dropColumn('min_qty');
            $table->dropColumn('max_qty');
            $table->dropColumn('last_sale');
            $table->dropColumn('lead_time');
            $table->dropColumn('expiry_date');
            $table->dropColumn('nhf_subsidiary');
            $table->dropColumn('is_active');
        });
    }
}
