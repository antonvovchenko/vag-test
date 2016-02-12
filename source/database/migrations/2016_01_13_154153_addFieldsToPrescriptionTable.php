<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPrescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prescription', function($table) {
            $table->string('total');
            $table->string('net_due');
            $table->string('insurance');
            $table->string('discount_percent');
            $table->string('discount_flat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prescription', function($table) {
            $table->dropColumn('total');
            $table->dropColumn('net_due');
            $table->dropColumn('insurance');
            $table->dropColumn('discount_percent');
            $table->dropColumn('discount_flat');
        });
    }
}
