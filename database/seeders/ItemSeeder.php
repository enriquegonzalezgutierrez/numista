<?php

// database/seeders/ItemSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as EloquentCollection;
// ADD THIS LINE
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\AttributeOption;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;

class ItemSeeder extends Seeder
{
    private EloquentCollection $attributes;

    private EloquentCollection $attributeOptions;

    public function run(): void
    {
        Item::truncate();
        $this->command->info('Cleaned previous items.');

        $this->attributes = SharedAttribute::all()->keyBy(fn ($attr) => strtolower(str_replace(' ', '_', $attr->name)));
        $this->attributeOptions = AttributeOption::all()->groupBy('shared_attribute_id');

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found. Skipping ItemSeeder.');

            return;
        }

        foreach ($tenants as $tenant) {
            $this->command->info("Seeding items for tenant: {$tenant->name}");
            $this->createItemsForTenant($tenant);

            $allItems = Item::where('tenant_id', $tenant->id)->get();
            $allCollections = Collection::where('tenant_id', $tenant->id)->get();
            if ($allCollections->isNotEmpty() && $allItems->isNotEmpty()) {
                $this->attachItemsToCollections($allItems, $allCollections);
            }
        }
    }

    private function createItemsForTenant(Tenant $tenant): void
    {
        $itemTypes = [
            ['category_name' => 'Moneda Española', 'type' => 'coin', 'count' => 10, 'for_sale' => true],
            ['category_name' => 'Relojes de Pulsera', 'type' => 'watch', 'count' => 5, 'for_sale' => true],
            ['category_name' => 'Marvel', 'type' => 'comic', 'count' => 8, 'for_sale' => false],
            ['category_name' => 'Sellos', 'type' => 'stamp', 'count' => 12, 'for_sale' => true],
        ];

        foreach ($itemTypes as $itemTypeInfo) {
            $this->createItemsForCategory($tenant, $itemTypeInfo['category_name'], $itemTypeInfo['type'], $itemTypeInfo['count'], $itemTypeInfo['for_sale']);
        }
    }

    private function createItemsForCategory(Tenant $tenant, string $categoryName, string $itemType, int $count, bool $isForSale): void
    {
        $category = Category::where('name', $categoryName)->first();
        if (! $category) {
            $this->command->warn("Global category '{$categoryName}' not found. Skipping item creation for this category.");

            return;
        }

        $imagePath = $this->copyPlaceholderImage($itemType, $tenant->id);

        for ($i = 0; $i < $count; $i++) {
            $itemData = Item::factory()->{$itemType}()->make();
            $salePrice = $isForSale ? ($itemData->purchase_price ?? 20) * 1.5 : null;

            $item = Item::create([
                'tenant_id' => $tenant->id,
                'name' => $itemData->name,
                'description' => $itemData->description,
                'type' => $itemData->type,
                'quantity' => 1,
                'purchase_price' => $itemData->purchase_price,
                'purchase_date' => $itemData->purchase_date,
                'status' => $isForSale ? 'for_sale' : 'in_collection',
                'sale_price' => $salePrice,
            ]);

            $item->categories()->attach($category->id);
            $this->attachAttributesForItem($item, $itemData->toArray());

            if ($imagePath) {
                $item->images()->create([
                    'path' => $imagePath,
                    'alt_text' => 'Image for '.$item->name,
                    'order_column' => 1,
                ]);
            }
        }
    }

    private function attachAttributesForItem(Item $item, array $itemData): void
    {
        $map = [
            'Year' => $itemData['year'] ?? null,
            'Country' => $itemData['country'] ?? null,
            'Grade' => $itemData['grade'] ?? null,
            'Material' => $itemData['material'] ?? null,
            'Denomination' => $itemData['denomination'] ?? null,
            'Mint Mark' => $itemData['mint_mark'] ?? null,
            'Composition' => $itemData['composition'] ?? null,
            'Weight' => $itemData['weight'] ?? null,
            'Serial Number' => $itemData['serial_number'] ?? null,
            'Publisher' => $itemData['publisher'] ?? null,
            'Issue Number' => $itemData['issue_number'] ?? null,
            'Cover Date' => $itemData['cover_date'] ?? null,
            'Author' => $itemData['author'] ?? null,
            'ISBN' => $itemData['isbn'] ?? null,
            'Brand' => $itemData['brand'] ?? null,
            'Model' => $itemData['model'] ?? null,
            'Face Value' => $itemData['face_value'] ?? null,
            'Artist' => $itemData['artist'] ?? null,
            'Dimensions' => $itemData['dimensions'] ?? null,
        ];

        $syncData = [];

        foreach ($map as $name => $value) {
            if ($value === null) {
                continue;
            }
            $attributeKey = strtolower(str_replace(' ', '_', $name));
            $attribute = $this->attributes->get($attributeKey);
            if (! $attribute) {
                continue;
            }

            $pivotData = ['value' => $value, 'attribute_option_id' => null];

            if ($attribute->type === 'select') {
                $option = $this->attributeOptions->get($attribute->id, collect())->firstWhere('value', $value);
                if ($option) {
                    $pivotData['attribute_option_id'] = $option->id;
                }
            }

            $syncData[$attribute->id] = $pivotData;
        }

        if (! empty($syncData)) {
            // THE FIX #2: Use the correct relationship name 'customAttributes'
            $item->customAttributes()->sync($syncData);
        }
    }

    private function attachItemsToCollections(EloquentCollection $items, EloquentCollection $collections): void
    {
        if ($items->count() < 5) {
            return;
        }
        $collections->each(function (Collection $collection) use ($items) {
            $itemsToAttach = $items->random(min($items->count(), rand(5, 10)))->pluck('id');
            $collection->items()->sync($itemsToAttach);
        });
    }

    private function copyPlaceholderImage(string $itemType, int $tenantId): ?string
    {
        $sourceDir = database_path('seeders/placeholders');
        $disk = Storage::disk('tenants');
        $targetDirectory = "tenant-{$tenantId}/item-images";
        $disk->makeDirectory($targetDirectory);

        $sourcePath = "{$sourceDir}/{$itemType}.png";
        if (! File::exists($sourcePath)) {
            $sourcePath = "{$sourceDir}/object.png";
        }
        if (! File::exists($sourcePath)) {
            return null;
        }

        $newFilename = uniqid().'-'.basename($sourcePath);
        $newPath = "{$targetDirectory}/{$newFilename}";
        $disk->put($newPath, File::get($sourcePath));

        return $newPath;
    }
}
