<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsuranceCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_card', function (Blueprint $table) {
            $table->increments('id');
            $table->string('card_data_string');
            $table->string('bin_number');
            $table->string('group_number');
            $table->string('subscriber_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('person_code');
            $table->string('relationship_code');
            $table->string('sex_code');
            $table->string('date_of_birth');
            $table->string('effective_date');
            $table->string('expiry_date');
            $table->string('card_id');
            $table->string('version_number');
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
        Schema::drop('insurance_card');
    }
}
