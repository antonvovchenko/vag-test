<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\LabelCode;

class LabelCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('label_code')->delete();

        $codes = array(
            [
                'title' => '10D',
                'description' => 'FOR TEN (10) DAYS.'
            ],
            [
                'title' => '1O',
                'description' => 'INSERT ONE (1) OVULE'
            ],
            [
                'title' => 'DBA',
                'description' => 'DISCARD THE BALANCE'
            ],
            [
                'title' => 'DO',
                'description' => 'PAIN AND FEVER'
            ],
            [
                'title' => 'KR',
                'description' => '***KEEP REFRIGERATED***'
            ]
        );

        foreach ($codes as $code)
        {
            LabelCode::create($code);
        }

        Model::reguard();
    }
}
