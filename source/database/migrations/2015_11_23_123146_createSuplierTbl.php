<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuplierTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('street')->nullable();
            $table->string('town')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('eorder_number')->nullable();

            $table->timestamp('last_date')->nullable();
            $table->string('lead_time')->nullable();

            $table->string('contact')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();

            $table->string('cc_number')->nullable();
            $table->string('cc_expiration_date')->nullable();
            $table->string('csc')->nullable();
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
        Schema::drop('supplier');
    }
}
