<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidFieldsToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function($table) {
            $table->string('paid_type')->nullable();
            $table->string('multi_payment_cash')->nullable();
            $table->string('multi_payment_cheque')->nullable();
            $table->string('multi_payment_credit_card')->nullable();
            $table->string('multi_payment_other')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function($table) {
            $table->dropColumn('paid_type');
            $table->dropColumn('multi_payment_cash');
            $table->dropColumn('multi_payment_cheque');
            $table->dropColumn('multi_payment_credit_card');
            $table->dropColumn('multi_payment_other');
        });
    }
}
