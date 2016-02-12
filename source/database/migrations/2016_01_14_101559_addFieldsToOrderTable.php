<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function($table) {
            $table->string('total');
            $table->string('taxes');
            $table->string('discount');
            $table->string('net_due');
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
            $table->dropColumn('total');
            $table->dropColumn('taxes');
            $table->dropColumn('discount');
            $table->dropColumn('net_due');
        });
    }
}
