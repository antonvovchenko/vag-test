<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToPrescriptionItemTable extends Migration
{
    public function up()
    {
        Schema::table('prescription_item', function($table) {
            $table->string('total')->nullable();
            $table->string('insurance')->nullable();
            $table->boolean('is_readable')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prescription_item', function($table) {
            $table->dropColumn('total');
            $table->dropColumn('insurance');
            $table->dropColumn('is_readable');
        });
    }
}
