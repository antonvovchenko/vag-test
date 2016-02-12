<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient', function($table) {
            $table->dropColumn('town');
            $table->dropColumn('parish');
            $table->dropColumn('street');

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient', function($table) {
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('phone');

            $table->string('town')->nullable();
            $table->string('parish')->nullable();
            $table->string('street')->nullable();
        });
    }
}
