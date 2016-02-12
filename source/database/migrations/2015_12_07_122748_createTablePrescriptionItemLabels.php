<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrescriptionItemLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescription_item_label_code', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('prescription_item_id')->unsigned();
            $table->integer('label_code_id')->unsigned();
        });
        Schema::table('prescription_item', function($table) {
            $table->dropColumn('labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prescription_item_label_code');
        Schema::table('prescription_item', function($table) {
            $table->string('labels')->nullable();
        });
    }
}
