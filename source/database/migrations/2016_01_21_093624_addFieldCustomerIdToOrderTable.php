<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldCustomerIdToOrderTable extends Migration
{
    public function up()
    {
        Schema::table('order', function($table) {
            $table->integer('customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function($table) {
            $table->dropColumn('customer_id');
        });
    }
}
