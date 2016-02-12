<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tax;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('tax')->delete();

        $taxes = array(
            [
                'value' => '0.00'
            ],
            [
                'value' => '16.50'
            ],
            [
                'value' => '25.50'
            ]
        );

        foreach ($taxes as $tax)
        {
            Tax::create($tax);
        }

        Model::reguard();
    }
}
