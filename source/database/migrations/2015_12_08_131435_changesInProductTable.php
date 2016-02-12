<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesInProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function($table) {
            $table->dropColumn('product_title');
            $table->dropColumn('unit_landed');
            $table->dropColumn('selling_price');
            $table->dropColumn('retail_markup');
            $table->dropColumn('unit_selling');
            $table->dropColumn('batch_number');
            $table->dropColumn('label_def');
            $table->dropColumn('balance');
            $table->dropColumn('nhf_subsidiary');
            $table->dropColumn('last_purchase');
            $table->dropColumn('last_sale');

            $table->string('pkg_price')->nullable();
            $table->string('pkg_cost')->nullable();
            $table->string('unit_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function($table) {

            $table->dropColumn('pkg_price');
            $table->dropColumn('pkg_cost');
            $table->dropColumn('unit_price');

            $table->string('product_title')->nullable();
            $table->string('unit_landed')->nullable();
            $table->string('selling_price')->nullable();
            $table->string('retail_markup')->nullable();
            $table->string('unit_selling')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('label_def')->nullable();
            $table->string('balance')->nullable();
            $table->string('nhf_subsidiary')->nullable();
            $table->string('last_purchase')->nullable();
            $table->string('last_sale')->nullable();
        });
    }
}
