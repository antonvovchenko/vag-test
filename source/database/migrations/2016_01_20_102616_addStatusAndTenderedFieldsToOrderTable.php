<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusAndTenderedFieldsToOrderTable extends Migration
{
    public function up()
    {
        Schema::table('order', function($table) {
            $table->string('status')->default('in_process');
            $table->string('tendered')->nullable();
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
            $table->dropColumn('status');
            $table->dropColumn('tendered');
        });
    }
}
