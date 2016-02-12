<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('supplier')->delete();

        $cats = [
            [
                'name' => 'ADVANCED TECHNOLOGY',
                'address' => '2 FERNLEIGH AVE',
                'city' => 'KINGSTON',
                'phone' => '786-4324'
            ],
            [
                'name' => 'BLUE DISTRIBUTORS LTD',
                'address' => '4-6 NORMAN RD.',
                'city' => 'KINGSTON',
                'phone' => '930-6405'
            ],
            [
                'name' => 'GILLETTE CARIBBEAN LTD',
                'address' => '21 GORDON TOWN ROAD',
                'city' => 'KINGSTON 6',
                'phone' => '9277707/7451'
            ],
        ];

        // Loop through each user above and create the record for them in the database
        foreach ($cats as $cat)
        {
            Supplier::create($cat);
        }

        Model::reguard();
    }
}
