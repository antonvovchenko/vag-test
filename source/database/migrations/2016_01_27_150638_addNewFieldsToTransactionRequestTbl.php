<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToTransactionRequestTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_request', function($table) {
            $table->string('actual')->nullable();
            $table->string('fee')->nullable();
            $table->string('tax')->nullable();
            $table->string('paid')->nullable();
            $table->string('reference_id')->nullable();
            $table->boolean('is_payment_confirmed')->default(0);
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
            $table->dropColumn('actual');
            $table->dropColumn('fee');
            $table->dropColumn('tax');
            $table->dropColumn('paid');
            $table->dropColumn('is_payment_confirmed');
        });
    }
}
