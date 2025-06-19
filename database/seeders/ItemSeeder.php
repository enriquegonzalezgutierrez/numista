<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the tenant created in the DevelopmentSeeder
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();

        // If the tenant doesn't exist, skip this seeder.
        if (! $tenant) {
            $this->command->warn('Tenant "coleccion-numista" not found. Skipping ItemSeeder.');
            return;
        }

        // Use the factory to create a variety of items for this tenant
        Item::factory(10)->coin()->create(['tenant_id' => $tenant->id]);
        Item::factory(10)->banknote()->create(['tenant_id' => $tenant->id]);
        Item::factory(8)->comic()->create(['tenant_id' => $tenant->id]);

        $this->command->info('Item seeder finished. Items created for tenant: ' . $tenant->name);
    }
}