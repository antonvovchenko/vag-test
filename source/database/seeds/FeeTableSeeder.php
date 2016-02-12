<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fee;

class FeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('fee')->delete();

        $fees = array(
            [
                'card_type' => '0-FE',
                'sort' => '05',
                'description' => 'INSURANCE ONE CARD',
                'fees' => '50.00'
            ],
            [
                'card_type' => '1-FE',
                'sort' => '66',
                'description' => 'INSURANCE TWO CARDS',
                'fees' => '100.00'
            ],
            [
                'card_type' => '2-FE',
                'sort' => '77',
                'description' => 'INSURANCE THREE CARD',
                'fees' => '200.00'
            ],
            [
                'card_type' => '3-FE',
                'sort' => '88',
                'description' => 'INSURANCE FOUR CARDS',
                'fees' => '200.00'
            ],
            [
                'card_type' => '4-FE',
                'sort' => '89',
                'description' => 'INSURANCE FIVE CARDS',
                'fees' => '200.00'
            ],
            [
                'card_type' => '5-FE',
                'sort' => '94',
                'description' => 'INSURANCE SIX CARDS',
                'fees' => '200.00'
            ],
            [
                'card_type' => 'J-JA',
                'sort' => '97',
                'description' => 'JADEP',
                'fees' => '40'
            ]
        );

        foreach ($fees as $fee)
        {
            Fee::create($fee);
        }

        Model::reguard();
    }
}
