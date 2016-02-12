<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesInTablePrescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prescription', function($table) {
            $table->dropColumn('is_completed');
            $table->string('cost')->nullable();
        });
        Schema::table('supplier', function($table) {
            $table->dropColumn('town');
            $table->dropColumn('street');
            $table->dropColumn('cc_number');
            $table->dropColumn('cc_expiration_date');
            $table->dropColumn('csc');

            $table->string('address')->nullable();
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
            $table->boolean('is_completed')->default(false);
            $table->dropColumn('cost');
        });
        Schema::table('supplier', function($table) {
            $table->dropColumn('address');

            $table->string('town')->nullable();
            $table->string('street')->nullable();
            $table->string('cc_number')->nullable();
            $table->string('cc_expiration_date')->nullable();
            $table->string('csc')->nullable();
        });
    }
}
