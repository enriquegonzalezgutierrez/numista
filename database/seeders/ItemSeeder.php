<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as EloquentCollection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class ItemSeeder extends Seeder
{
    /**
     * @var \Illuminate\Support\Collection<string, \Numista\Collection\Domain\Models\Attribute>
     */
    private EloquentCollection $attributes;

    public function run(): void
    {
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (! $tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping ItemSeeder.');

            return;
        }

        $this->attributes = Attribute::where('tenant_id', $tenant->id)->get()->keyBy(fn ($attr) => strtolower(str_replace(' ', '_', $attr->name)));

        // THE FIX: Only delete the item-specific directory, not the entire tenant directory.
        Item::where('tenant_id', $tenant->id)->get()->each(fn ($item) => $item->delete());
        Storage::disk('tenants')->deleteDirectory("tenant-{$tenant->id}/item-images");
        $this->command->info('Cleaned previous items and item images for the tenant.');

        $this->command->info('Creating a large volume of items with multiple placeholders...');

        $this->createItemsForCategory($tenant, 'moneda-espanola', 'coin', 100, true);
        $this->createItemsForCategory($tenant, 'marvel', 'comic', 50);
        $this->createItemsForCategory($tenant, 'relojes-de-pulsera', 'watch', 75, true);
        $this->createItemsForCategory($tenant, 'sellos', 'stamp', 150, true);
        $this->createItemsForCategory($tenant, 'libros-y-manuscritos', 'book', 75);
        $this->createItemsForCategory($tenant, 'arte-y-antiguedades', 'art', 50, true);

        // Assign items to collections
        $allItems = Item::where('tenant_id', $tenant->id)->get();
        $allCollections = Collection::where('tenant_id', $tenant->id)->get();

        if ($allCollections->isNotEmpty() && $allItems->isNotEmpty()) {
            $this->attachItemsToCollections($allItems, $allCollections);
        }

        $this->command->info("Item seeder finished. {$allItems->count()} items created with relations and images.");
    }

    private function createItemsForCategory(Tenant $tenant, string $categorySlug, string $itemType, int $count, bool $isForSale = false): void
    {
        $category = Category::where('slug', $categorySlug)->first();
        if (! $category) {
            $this->command->warn("Category '{$categorySlug}' not found. Skipping item creation.");

            return;
        }

        $imagePath = $this->copyPlaceholderImage($itemType, $tenant->id);

        for ($i = 0; $i < $count; $i++) {
            $itemData = Item::factory()->{$itemType}()->make(['tenant_id' => $tenant->id]);
            $item = Item::create([
                'tenant_id' => $tenant->id,
                'name' => $itemData->name,
                'description' => $itemData->description,
                'type' => $itemData->type,
                'quantity' => $itemData->quantity,
                'purchase_price' => $itemData->purchase_price,
                'purchase_date' => $itemData->purchase_date,
                'status' => $isForSale ? 'for_sale' : 'in_collection',
                'sale_price' => $isForSale ? $itemData->purchase_price * fake()->randomFloat(2, 1.2, 2.5) : null,
            ]);
            $item->categories()->attach($category->id);
            $this->attachAllAttributes($item, $itemData, $itemType);
            if ($imagePath) {
                $item->images()->create(['path' => $imagePath, 'alt_text' => 'Main image for '.$item->name, 'order_column' => 1]);
                $secondaryImageCount = rand(2, 4);
                for ($j = 2; $j <= $secondaryImageCount + 1; $j++) {
                    $item->images()->create(['path' => $imagePath, 'alt_text' => "Secondary image {$j} for ".$item->name, 'order_column' => $j]);
                }
            }
        }
    }

    private function attachAllAttributes(Item $item, Item $itemData, string $itemType): void
    {
        $this->attachAttribute($item, 'Grade', $itemData->grade ?? null);
        $this->attachAttribute($item, 'Year', $itemData->year ?? null);
        $this->attachAttribute($item, 'Country', $itemData->country?->name ?? null);
        switch ($itemType) {
            case 'coin':
                $this->attachAttribute($item, 'Denomination', $itemData->denomination);
                $this->attachAttribute($item, 'Mint Mark', $itemData->mint_mark);
                $this->attachAttribute($item, 'Composition', $itemData->composition);
                $this->attachAttribute($item, 'Weight', $itemData->weight);
                break;
            case 'banknote':
                $this->attachAttribute($item, 'Denomination', $itemData->denomination);
                $this->attachAttribute($item, 'Serial Number', $itemData->serial_number);
                break;
            case 'comic':
                $this->attachAttribute($item, 'Publisher', $itemData->publisher);
                $this->attachAttribute($item, 'Issue Number', $itemData->issue_number);
                $this->attachAttribute($item, 'Cover Date', $itemData->cover_date);
                break;
            case 'watch':
                $this->attachAttribute($item, 'Brand', $itemData->brand);
                $this->attachAttribute($item, 'Model', $itemData->model);
                $this->attachAttribute($item, 'Material', $itemData->material);
                break;
            case 'stamp':
                $this->attachAttribute($item, 'Face Value', $itemData->face_value);
                break;
            case 'book':
                $this->attachAttribute($item, 'Author', $itemData->author);
                $this->attachAttribute($item, 'Publisher', $itemData->publisher);
                $this->attachAttribute($item, 'ISBN', $itemData->isbn);
                break;
            case 'art':
                $this->attachAttribute($item, 'Artist', $itemData->artist);
                $this->attachAttribute($item, 'Dimensions', $itemData->dimensions);
                $this->attachAttribute($item, 'Material', $itemData->material);
                break;
        }
    }

    private function attachAttribute(Item $item, string $attributeName, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $attributeKey = strtolower(str_replace(' ', '_', $attributeName));
        $attribute = $this->attributes->get($attributeKey);

        if ($attribute) {
            $pivotData = ['value' => $value];
            if ($attribute->type === 'select') {
                $attributeValue = $attribute->values()->where('value', $value)->first();
                if ($attributeValue) {
                    $pivotData['attribute_value_id'] = $attributeValue->id;
                }
            }
            $item->attributes()->attach($attribute->id, $pivotData);
        }
    }

    private function attachItemsToCollections(EloquentCollection $items, EloquentCollection $collections): void
    {
        $collections->each(function (Collection $collection) use ($items) {
            $itemsToAttach = $items->random(min($items->count(), rand(15, 50)))->pluck('id');
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
            if (! File::exists($sourcePath)) {
                $this->command->warn("Placeholder not found for '{$itemType}.png' or 'object.png'.");

                return null;
            }
        }
        $newFilename = uniqid().'-'.basename($sourcePath);
        $newPath = "{$targetDirectory}/{$newFilename}";
        $disk->put($newPath, File::get($sourcePath));

        return $newPath;
    }
}
