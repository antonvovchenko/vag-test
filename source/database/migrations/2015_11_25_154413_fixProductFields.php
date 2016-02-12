<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixProductFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function($table)
        {
            $table->dropColumn('generic');
            $table->boolean('is_generic')->default(0);
            $table->integer('generic_product_id')->nullable();
            $table->boolean('is_prescription')->default(0);

            $table->text('description')->nullable()->change();
            $table->string('ndc')->nullable()->change();
            $table->string('pkg_size')->nullable()->change();
            $table->string('unit_landed')->nullable()->change();
            $table->string('selling_price')->nullable()->change();
            $table->integer('tax_id')->nullable()->change();
            $table->string('barcode')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('unit_cost')->nullable()->change();
            $table->string('retail_markup')->nullable()->change();
            $table->string('unit_selling')->nullable()->change();
            $table->string('last_purchase')->nullable()->change();
            $table->string('batch_number')->nullable()->change();
            $table->string('label_def')->nullable()->change();
            $table->string('balance')->nullable()->change();
            $table->string('min_qty')->nullable()->change();
            $table->string('max_qty')->nullable()->change();
            $table->string('last_sale')->nullable()->change();
            $table->string('lead_time')->nullable()->change();
            $table->string('nhf_subsidiary')->nullable()->change();
            $table->boolean('is_active')->default(0)->change();

            $table->dropColumn('category_id');
            $table->integer('markup_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function($table)
        {
            $table->string('generic');
            $table->dropColumn('is_generic');
            $table->dropColumn('generic_product_id');
            $table->dropColumn('is_prescription');

            $table->dropColumn('markup_type_id');
            $table->integer('category_id')->nullable();
        });
    }
}
