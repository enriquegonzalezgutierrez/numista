<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Numista\Collection\Domain\Models\Item;

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

        // THE FIX: After all seeders have run, sync all Item models with the search engine.
        // This is the single source of truth for indexing after a fresh migration.
        if (app()->environment() !== 'testing') { // Optional: prevent running during tests if not needed
            $this->command->info('Importing items into search engine...');
            Artisan::call('scout:import', ['model' => Item::class]);
            $this->command->info('✅ Items successfully imported.');
        }
    }
}
