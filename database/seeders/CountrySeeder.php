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
        // Clear the table to avoid duplicates on re-seeding
        DB::table('countries')->delete();

        // Insert countries with Spanish names
        DB::table('countries')->insert([
            ['name' => 'España', 'iso_code' => 'ES'],
            ['name' => 'Estados Unidos', 'iso_code' => 'US'],
            ['name' => 'México', 'iso_code' => 'MX'],
            ['name' => 'Francia', 'iso_code' => 'FR'],
            ['name' => 'Alemania', 'iso_code' => 'DE'],
            // Add more countries as needed
        ]);
    }
}