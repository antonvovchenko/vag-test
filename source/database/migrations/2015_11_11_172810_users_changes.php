<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('logon_name');
            $table->string('full_name');
            $table->smallInteger('type');
            $table->string('pharmacist_registration');
            $table->boolean('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('logon_name');
            $table->dropColumn('full_name');
            $table->dropColumn('type');
            $table->dropColumn('pharmacist_registration');
            $table->dropColumn('is_active');
            $table->string('name');
        });
    }
}
