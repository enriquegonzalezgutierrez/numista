<?php

// database/seeders/SnapshotSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class SnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::factory()->create(['name' => 'Marketplace Test Collection']);

        // Create the items and store them in a collection variable.
        $items = Item::factory(15)
            ->sequence(fn ($sequence) => [
                'name' => "Item {$sequence->index}",
                'sale_price' => 10 + $sequence->index,
                'description' => "This is a predictable description for Item {$sequence->index}.",
            ])
            ->create([
                'status' => 'for_sale',
                'tenant_id' => $tenant->id,
            ]);

        // THE FINAL FIX: Instead of calling Artisan, we use the 'searchable' macro
        // provided by Scout on the collection of items we just created.
        // This runs in the same process as the test and correctly uses the in-memory database.
        $items->searchable();
    }
}
