<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldInsuranceProviderToInsuranceCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insurance_card', function($table) {
            $table->string('insurance_provider')->nullable()->after('card_data_string');
        });

        //update insurance_provider for all cards
        foreach (DB::table('insurance_card')->get() as $card) {
            $insurance_provider = \App\Library\TransactionService::getInsuranceProviderByBinNumber($card->bin_number);
            DB::table('insurance_card')->where('id', $card->id)->update([
                'insurance_provider' => $insurance_provider
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insurance_card', function($table) {
            $table->dropColumn('insurance_provider');
        });
    }
}
