<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The order is important here.
        // We need to create tenants, users, countries, and attributes
        // BEFORE we try to create items that depend on them.
        $this->call([
            // --- Core & Setup Seeders ---
            DevelopmentSeeder::class, // Creates the main tenant and admin user
            CountrySeeder::class,     // Creates reference countries
            AttributeSeeder::class,   // <-- THIS IS THE NEW ADDITION

            // --- Content Seeders ---
            CategorySeeder::class,    // Creates the category tree
            CollectionSeeder::class,  // Creates some collections
            ItemSeeder::class,        // Creates items and links them to attributes & categories
            OrderSeeder::class,       // Creates customers and orders with items
        ]);
    }
}