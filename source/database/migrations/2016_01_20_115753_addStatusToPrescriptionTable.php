<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToPrescriptionTable extends Migration
{
    public function up()
    {
        Schema::table('prescription', function($table) {
            $table->string('status')->default('in_process');
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
            $table->dropColumn('status');
        });
    }
}
