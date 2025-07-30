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

        Item::factory(15)
            ->sequence(fn ($sequence) => [
                'name' => "Item {$sequence->index}",
                'sale_price' => 10 + $sequence->index,
                // THE FIX: Add a predictable description
                'description' => "This is a predictable description for Item {$sequence->index}.",
            ])
            ->create([
                'status' => 'for_sale',
                'tenant_id' => $tenant->id,
            ]);
    }
}
