<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductMarkupType;

class ProductMarkupTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('product_markup_type')->delete();

        $cats = [
            [
                'title' => 'COSMETICS',
                'description' => 'COSMETIC',
                'default_markup' => '50.00'
            ],
            [
                'title' => 'LIVINGWELL',
                'description' => 'LIVING WELL',
                'default_markup' => '45.00'
            ],
            [
                'title' => 'STATIONARY',
                'description' => 'BOOKS',
                'default_markup' => '50.00'
            ]
        ];

        // Loop through each user above and create the record for them in the database
        foreach ($cats as $cat)
        {
            ProductMarkupType::create($cat);
        }

        Model::reguard();
    }
}
