<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->insert([
            ['name' => 'Spain', 'iso_code' => 'ES'],
            ['name' => 'United States', 'iso_code' => 'US'],
            ['name' => 'Mexico', 'iso_code' => 'MX'],
            ['name' => 'France', 'iso_code' => 'FR'],
            ['name' => 'Germany', 'iso_code' => 'DE'],
            // Add more countries as needed
        ]);
    }
}
