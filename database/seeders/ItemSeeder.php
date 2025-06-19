<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the tenant we created in the DevelopmentSeeder
        $tenant = Tenant::where('slug', 'numista-collection')->first();

        // If the tenant doesn't exist, don't do anything.
        if (! $tenant) {
            $this->command->warn('Tenant "numista-collection" not found. Skipping ItemSeeder.');
            return;
        }

        // Use the factory to create a variety of items for this tenant
        Item::factory(10)->coin()->create(['tenant_id' => $tenant->id]);
        Item::factory(10)->banknote()->create(['tenant_id' => $tenant->id]);
        Item::factory(8)->comic()->create(['tenant_id' => $tenant->id]);

        $this->command->info('Item seeder finished. Items created for tenant: ' . $tenant->name);
    }
}