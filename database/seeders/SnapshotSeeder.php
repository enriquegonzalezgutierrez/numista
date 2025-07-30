<?php

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
        // THE FIX: Create a single, predictable tenant for all items.
        $tenant = Tenant::factory()->create(['name' => 'Marketplace Test Collection']);

        // Create a predictable set of items for snapshot testing, all belonging to the same tenant.
        Item::factory(15)
            ->sequence(fn ($sequence) => [
                'name' => "Item {$sequence->index}",
                'sale_price' => 10 + $sequence->index,
            ])
            ->create([
                'status' => 'for_sale',
                'tenant_id' => $tenant->id, // Assign all items to the predictable tenant
            ]);
    }
}
