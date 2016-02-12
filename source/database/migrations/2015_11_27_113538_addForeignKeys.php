<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function ($table) {
            $table->renameColumn('last_supplier', 'supplier_id');
        });
        Schema::table('inventory', function ($table) {
            $table->integer('product_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->change();

            $table->foreign('product_id')->references('id')->on('product');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function ($table) {
            $table->dropForeign('inventory_product_id_foreign');
            $table->dropForeign('inventory_user_id_foreign');
            $table->dropForeign('inventory_supplier_id_foreign');
        });
    }
}
