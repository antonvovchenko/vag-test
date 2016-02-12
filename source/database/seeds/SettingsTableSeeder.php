<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('settings')->delete();

        $options = array(
            [ 'name' => 'common_session_lifetime', 'value' => '86400' ],

            [ 'name' => 'company_name', 'value' => 'BLD SOLUTION' ],

            [ 'name' => 'bcj_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'loj_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'eba_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'med_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'jadep_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'nhf_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'clk_provider_number', 'value' => 'AX123456' ],
            [ 'name' => 'medecus_provider_number', 'value' => 'AX123456' ],

            [ 'name' => 'bcj_bin_number', 'value' => '000030' ],
            [ 'name' => 'nhf_bin_number', 'value' => '000020' ],
            [ 'name' => 'eba_bin_number', 'value' => '000030' ],
            [ 'name' => 'flb_bin_number', 'value' => '000030' ],
            [ 'name' => 'clk_bin_number', 'value' => '000030' ],
            [ 'name' => 'med_bin_number', 'value' => '000090' ],

            [ 'name' => 'insurance_service_fee_percent', 'value' => '1.75' ],
            [ 'name' => 'gct_percent', 'value' => '16.50' ],

            [ 'name' => 'printers_default_print_type', 'value' => 'screen' ],
            [ 'name' => 'printers_prescription_print_type', 'value' => 'screen' ],
            [ 'name' => 'printers_prescription_labels_print_type', 'value' => 'screen' ],
            [ 'name' => 'printers_inventory_print_type', 'value' => 'screen' ]

        );

        foreach ($options as $option)
        {
            \App\Models\Settings::create($option);
        }

        Model::reguard();
    }
}
