<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DevelopmentSeeder::class,
            CountrySeeder::class,
            ItemTypeSeeder::class, // THE FIX: Add the new seeder here
            SharedAttributeSeeder::class,
            CategorySeeder::class,
            CollectionSeeder::class,
            ItemSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
