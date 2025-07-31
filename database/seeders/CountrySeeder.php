<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->delete();

        // THE FIX: Restore multiple countries to test the dynamic select
        DB::table('countries')->insert([
            ['name' => 'España', 'iso_code' => 'ES'],
            ['name' => 'Estados Unidos', 'iso_code' => 'US'],
            ['name' => 'México', 'iso_code' => 'MX'],
            ['name' => 'Francia', 'iso_code' => 'FR'],
            ['name' => 'Alemania', 'iso_code' => 'DE'],
        ]);
    }
}
