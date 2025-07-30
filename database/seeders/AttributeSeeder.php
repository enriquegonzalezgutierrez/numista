<?php

// database/seeders/AttributeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Tenant;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        Attribute::truncate();
        DB::table('attribute_item_type')->truncate();
        $this->command->info('Cleaned previous attributes.');

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found, skipping AttributeSeeder.');

            return;
        }

        $this->command->info('Defining item attributes for all tenants...');

        foreach ($tenants as $tenant) {
            // --- Common Attributes ---
            $this->createAttribute($tenant, 'Year', 'number', ['art', 'banknote', 'book', 'coin', 'stamp', 'watch']);
            $this->createAttribute($tenant, 'Country', 'text', ['banknote', 'coin', 'stamp']);
            $this->createAttribute($tenant, 'Grade', 'select', ['coin', 'banknote', 'comic']);
            $this->createAttribute($tenant, 'Material', 'text', ['art', 'watch']);

            // --- Type-Specific Attributes ---
            $this->createAttribute($tenant, 'Denomination', 'text', ['coin', 'banknote']);
            $this->createAttribute($tenant, 'Mint Mark', 'text', ['coin']);
            $this->createAttribute($tenant, 'Composition', 'text', ['coin']);
            $this->createAttribute($tenant, 'Weight', 'number', ['coin']);
            $this->createAttribute($tenant, 'Serial Number', 'text', ['banknote']);
            $this->createAttribute($tenant, 'Publisher', 'text', ['book', 'comic']);
            $this->createAttribute($tenant, 'Issue Number', 'text', ['comic']);
            $this->createAttribute($tenant, 'Cover Date', 'date', ['comic']);
            $this->createAttribute($tenant, 'Author', 'text', ['book']);
            $this->createAttribute($tenant, 'ISBN', 'text', ['book']);
            $this->createAttribute($tenant, 'Brand', 'text', ['watch']);
            $this->createAttribute($tenant, 'Model', 'text', ['watch']);
            $this->createAttribute($tenant, 'Face Value', 'text', ['stamp']);
            $this->createAttribute($tenant, 'Artist', 'text', ['art']);
            $this->createAttribute($tenant, 'Dimensions', 'text', ['art']);
        }
    }

    private function createAttribute(Tenant $tenant, string $name, string $type, array $itemTypes): void
    {
        $attribute = Attribute::create([
            'tenant_id' => $tenant->id,
            'name' => $name,
            'type' => $type,
            'is_filterable' => in_array($name, ['Year', 'Country', 'Grade', 'Brand', 'Material', 'Artist', 'Publisher']),
        ]);

        $pivots = collect($itemTypes)->map(fn ($itemType) => [
            'attribute_id' => $attribute->id,
            'item_type' => $itemType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($pivots->isNotEmpty()) {
            DB::table('attribute_item_type')->insert($pivots->all());
        }
    }
}
