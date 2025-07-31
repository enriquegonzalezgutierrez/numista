<?php

// database/seeders/SharedAttributeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\AttributeOption;
use Numista\Collection\Domain\Models\ItemType;
use Numista\Collection\Domain\Models\SharedAttribute;

class SharedAttributeSeeder extends Seeder
{
    public function run(): void
    {
        SharedAttribute::truncate();
        AttributeOption::truncate();
        DB::table('shared_attribute_item_type')->truncate();
        $this->command->info('Cleaned previous shared attributes, options, and item type links.');
        $this->command->info('Defining shared item attributes and linking them to item types...');

        $itemTypes = ItemType::all()->pluck('id', 'name');

        // --- Common Attributes ---
        // THE FIX: Ensure all types created by ItemSeeder are included where appropriate.
        // 'art' and 'book' are not used by ItemSeeder, but we keep them for completeness.
        $this->createAttribute('Year', 'number', $itemTypes->only(['art', 'banknote', 'book', 'coin', 'stamp', 'watch'])->values()->all(), true);
        $this->createAttribute('Country', 'text', $itemTypes->only(['banknote', 'coin', 'stamp'])->values()->all(), true);
        $this->createAttribute('Material', 'text', $itemTypes->only(['art', 'watch'])->values()->all(), true);

        // --- Type-Specific Attributes ---
        $this->createAttribute('Denomination', 'text', $itemTypes->only(['coin', 'banknote'])->values()->all());
        $this->createAttribute('Mint Mark', 'text', $itemTypes->only(['coin'])->values()->all());
        $this->createAttribute('Composition', 'text', $itemTypes->only(['coin'])->values()->all());
        $this->createAttribute('Weight', 'number', $itemTypes->only(['coin'])->values()->all());
        $this->createAttribute('Serial Number', 'text', $itemTypes->only(['banknote'])->values()->all());
        $this->createAttribute('Publisher', 'text', $itemTypes->only(['book', 'comic'])->values()->all(), true);
        $this->createAttribute('Issue Number', 'text', $itemTypes->only(['comic'])->values()->all());
        $this->createAttribute('Cover Date', 'date', $itemTypes->only(['comic'])->values()->all());
        $this->createAttribute('Author', 'text', $itemTypes->only(['book'])->values()->all());
        $this->createAttribute('ISBN', 'text', $itemTypes->only(['book'])->values()->all());
        $this->createAttribute('Brand', 'text', $itemTypes->only(['watch'])->values()->all(), true); // Correctly linked to 'watch'
        $this->createAttribute('Model', 'text', $itemTypes->only(['watch'])->values()->all());
        $this->createAttribute('Face Value', 'text', $itemTypes->only(['stamp'])->values()->all());
        $this->createAttribute('Artist', 'text', $itemTypes->only(['art'])->values()->all(), true);
        $this->createAttribute('Dimensions', 'text', $itemTypes->only(['art'])->values()->all());

        // --- Selectable Attributes ---
        $this->createSelectAttribute('Grade', $itemTypes->only(['coin', 'banknote', 'comic'])->values()->all(), true, ['unc', 'au', 'xf', 'vf', 'f', 'g']);
    }

    // ... (los métodos createAttribute y createSelectAttribute se mantienen igual)
    // ...
    private function createAttribute(string $name, string $type, array $itemTypeIds, bool $isFilterable = false): SharedAttribute
    {
        $attribute = SharedAttribute::create([
            'name' => $name,
            'type' => $type,
            'is_filterable' => $isFilterable,
        ]);

        $attribute->itemTypes()->sync($itemTypeIds);

        return $attribute;
    }

    private function createSelectAttribute(string $name, array $itemTypeIds, bool $isFilterable, array $options): void
    {
        $attribute = $this->createAttribute($name, 'select', $itemTypeIds, $isFilterable);

        foreach ($options as $optionValue) {
            $attribute->options()->create(['value' => $optionValue]);
        }
    }
}
