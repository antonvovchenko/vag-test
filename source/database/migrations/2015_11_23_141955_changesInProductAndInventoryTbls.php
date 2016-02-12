<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesInProductAndInventoryTbls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('last_supplier');
            $table->dropColumn('expiry_date');
        });
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('last_supplier');
            $table->timestamp('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('last_supplier');
            $table->timestamp('expiry_date');
        });
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('last_supplier');
            $table->dropColumn('expiry_date');
        });
    }
}
