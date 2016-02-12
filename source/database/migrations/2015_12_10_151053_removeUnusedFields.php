<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prescription', function($table) {
            $table->dropColumn('cost');
        });
        Schema::table('prescription_item', function($table) {
            $table->dropColumn('total_cost');
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
            $table->string('cost')->nullable();
        });
        Schema::table('prescription_item', function($table) {
            $table->string('total_cost')->nullable();
        });
    }
}
