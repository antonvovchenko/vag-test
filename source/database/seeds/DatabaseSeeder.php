<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UsersTableSeeder::class);
        $this->call(ProductMarkupTypeTableSeeder::class);
        $this->call(SuppliersTableSeeder::class);
        $this->call(LabelCodeSeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(SettingsTableSeeder::class);

        Model::reguard();
    }
}
