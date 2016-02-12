<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesInTransactionRequestTable2 extends Migration
{
    public function up()
    {
        Schema::table('transaction_request', function($table) {
            $table->string('status');
            $table->string('type');
            $table->string('adjudicate_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_request', function($table) {
            $table->dropColumn('status');
            $table->dropColumn('type');
            $table->dropColumn('adjudicate_request_id');
        });
    }
}
