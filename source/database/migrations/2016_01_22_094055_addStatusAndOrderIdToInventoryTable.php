<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddStatusAndOrderIdToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function($table) {
            $table->string('type')->default('in');
            $table->integer('order_id')->nullable();
            $table->integer('batch_id')->nullable();

            $table->dropColumn('is_active');
        });

        //truncate some tables
        DB::table('order_item')->truncate();
        DB::table('order')->truncate();
        DB::table('prescription_item_label_code')->truncate();
        DB::table('prescription_insurance_card')->truncate();
        DB::table('prescription_item')->truncate();
        DB::table('prescription')->truncate();
        DB::table('inventory')->truncate();

        DB::table('product')->update([
            'qoh' => 0
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function($table) {
            $table->dropColumn('type');
            $table->dropColumn('order_id');
            $table->dropColumn('batch_id');

            $table->boolean('is_active')->default(true);
        });
    }
}
