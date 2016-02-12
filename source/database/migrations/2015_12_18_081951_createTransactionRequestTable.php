<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_request', function (Blueprint $table) {
            $table->increments('id');
            $table->text('request');
            $table->text('response');
            $table->string('socket');
            $table->string('ip');
            $table->string('port');
            $table->integer('prescription_item_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transaction_request');
    }
}
