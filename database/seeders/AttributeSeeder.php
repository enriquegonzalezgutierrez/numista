<?php
// database/seeders/AttributeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Tenant;

class AttributeSeeder extends Seeder
{
    /**
     * @var Tenant|null
     */
    private ?Tenant $tenant;

    public function run(): void
    {
        $this->tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (!$this->tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping AttributeSeeder.');
            return;
        }

        Attribute::where('tenant_id', $this->tenant->id)->delete();
        // The pivot table is automatically truncated due to cascade on delete.

        $this->command->info('Defining item attributes and their types...');

        // --- COMMON ATTRIBUTES ---
        $this->createAttribute('Year', 'number', ['art', 'banknote', 'book', 'camera', 'coin', 'movie_collectible', 'photo', 'postcard', 'radio', 'stamp', 'toy', 'vehicle', 'vinyl_record', 'watch']);
        $this->createAttribute('Country', 'text', ['art', 'banknote', 'coin', 'military', 'stamp']);
        $this->createAttribute('Grade', 'text', ['banknote', 'coin', 'comic', 'stamp']);
        $this->createAttribute('Brand', 'text', ['camera', 'pen', 'radio', 'toy', 'vehicle', 'watch']);
        $this->createAttribute('Model', 'text', ['camera', 'pen', 'vehicle', 'watch']);
        $this->createAttribute('Material', 'text', ['antique', 'art', 'craftsmanship', 'jewelry', 'medal', 'military', 'pen', 'toy', 'vintage_item', 'watch']);
        $this->createAttribute('Artist', 'text', ['art', 'craftsmanship', 'vinyl_record']);
        $this->createAttribute('Publisher', 'text', ['book', 'comic', 'postcard']);
        
        // --- COIN & BANKNOTE ---
        $this->createAttribute('Denomination', 'text', ['coin', 'banknote']);
        $this->createAttribute('Mint Mark', 'text', ['coin']);
        $this->createAttribute('Composition', 'text', ['coin', 'medal']);
        $this->createAttribute('Weight', 'number', ['coin', 'jewelry', 'medal']);
        $this->createAttribute('Serial Number', 'text', ['banknote']);
        
        // --- PAPER & MEDIA ---
        $this->createAttribute('Issue Number', 'text', ['comic']);
        $this->createAttribute('Cover Date', 'date', ['comic']);
        $this->createAttribute('Author', 'text', ['book']);
        $this->createAttribute('ISBN', 'text', ['book']);
        $this->createAttribute('Record Label', 'text', ['vinyl_record']);
        $this->createAttribute('Face Value', 'text', ['stamp']);
        
        // --- ART & PHOTOGRAPHY ---
        $this->createAttribute('Dimensions', 'text', ['art', 'photo']);
        $this->createAttribute('Photographer', 'text', ['photo']);
        $this->createAttribute('Location', 'text', ['photo', 'postcard']);
        $this->createAttribute('Technique', 'text', ['art', 'photo', 'craftsmanship']);
        
        // --- VEHICLE ---
        $this->createAttribute('License Plate', 'text', ['vehicle']);
        $this->createAttribute('Chassis Number', 'text', ['vehicle']);
        
        // --- SPECIALIZED COLLECTIBLES ---
        $this->createAttribute('Gemstone', 'text', ['jewelry']);
        $this->createAttribute('Conflict', 'text', ['military']);
        $this->createAttribute('Sport', 'text', ['sports']);
        $this->createAttribute('Team', 'text', ['sports']);
        $this->createAttribute('Player', 'text', ['sports']);
        $this->createAttribute('Event', 'text', ['sports']);
        $this->createAttribute('Movie Title', 'text', ['movie_collectible']);
        $this->createAttribute('Character', 'text', ['movie_collectible', 'toy']);
    }

    private function createAttribute(string $name, string $type, array $itemTypes): void
    {
        if (!$this->tenant) {
            return;
        }

        /** @var Attribute $attribute */
        $attribute = Attribute::create([
            'tenant_id' => $this->tenant->id,
            'name' => $name,
            'type' => $type,
            'is_filterable' => in_array($name, ['Year', 'Country', 'Grade', 'Brand', 'Material', 'Artist', 'Publisher']), // Example filterable attributes
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