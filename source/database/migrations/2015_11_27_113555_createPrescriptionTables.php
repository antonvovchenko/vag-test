<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrescriptionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriber', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('registration_number');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('patient', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('town')->nullable();
            $table->string('parish')->nullable();
            $table->string('street')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('prescription', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->integer('pharmacist_user_id')->unsigned();
            $table->integer('creator_user_id')->unsigned();
            $table->integer('prescriber_id')->unsigned();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient');
            $table->foreign('pharmacist_user_id')->references('id')->on('users');
            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->foreign('prescriber_id')->references('id')->on('prescriber');
        });
        Schema::create('prescription_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('prescription_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('qty');
            $table->integer('course_days')->nullable();
            $table->integer('refill_allowed_times')->nullable();
            $table->string('total_cost')->nullable();
            $table->text('labels')->nullable();
            $table->timestamps();

            $table->foreign('prescription_id')->references('id')->on('prescription');
            $table->foreign('product_id')->references('id')->on('product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prescription', function ($table) {
            $table->dropForeign('prescription_patient_id_foreign');
            $table->dropForeign('prescription_pharmacist_user_id_foreign');
            $table->dropForeign('prescription_creator_user_id_foreign');
            $table->dropForeign('prescription_prescriber_id_foreign');
        });
        Schema::table('prescription_item', function ($table) {
            $table->dropForeign('prescription_item_prescription_id_foreign');
            $table->dropForeign('prescription_item_product_id_foreign');
        });

        Schema::drop('prescription_item');
        Schema::drop('prescription');
        Schema::drop('prescriber');
        Schema::drop('patient');
    }
}
