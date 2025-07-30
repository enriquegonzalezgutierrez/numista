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
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            CategorySeeder::class,
            CollectionSeeder::class,
            ItemSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
