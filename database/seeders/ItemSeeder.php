<?php

// database/seeders/ItemSeeder.php

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
    private EloquentCollection $attributes;

    public function run(): void
    {
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (! $tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping ItemSeeder.');

            return;
        }

        $this->attributes = Attribute::where('tenant_id', $tenant->id)->get()->keyBy(fn ($attr) => strtolower(str_replace(' ', '_', $attr->name)));

        // Clean previous items and images for the tenant
        Item::where('tenant_id', $tenant->id)->get()->each(fn ($item) => $item->delete());
        Storage::disk('tenants')->deleteDirectory("tenant-{$tenant->id}");
        $this->command->info('Cleaned previous items and images for the tenant.');

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

            // --- THE FIX: Create multiple image records for each item ---
            if ($imagePath) {
                // 1. Create the main image record
                $item->images()->create([
                    'path' => $imagePath,
                    'alt_text' => 'Main image for '.$item->name,
                    'order_column' => 1,
                ]);

                // 2. Create a few additional, secondary image records
                $secondaryImageCount = rand(2, 4); // Create between 2 and 4 secondary images
                for ($j = 2; $j <= $secondaryImageCount + 1; $j++) {
                    $item->images()->create([
                        'path' => $imagePath, // Using the same image path
                        'alt_text' => "Secondary image {$j} for ".$item->name,
                        'order_column' => $j,
                    ]);
                }
            }
        }
    }

    // ... (El resto de los métodos: attachAllAttributes, attachAttribute, attachItemsToCollections, copyPlaceholderImage)
    // se quedan exactamente igual que en la versión anterior.
    private function attachAllAttributes(Item $item, Item $itemData, string $itemType): void
    {
        $this->attachAttribute($item, 'Grado', $itemData->grade ?? null);
        $this->attachAttribute($item, 'Año', $itemData->year ?? null);
        $this->attachAttribute($item, 'País', $itemData->country?->name ?? null);
        switch ($itemType) {
            case 'coin':
                $this->attachAttribute($item, 'Denominación', $itemData->denomination);
                $this->attachAttribute($item, 'Marca de Ceca', $itemData->mint_mark);
                $this->attachAttribute($item, 'Composición', $itemData->composition);
                $this->attachAttribute($item, 'Peso', $itemData->weight);
                break;
            case 'banknote':
                $this->attachAttribute($item, 'Denominación', $itemData->denomination);
                $this->attachAttribute($item, 'Número de Serie', $itemData->serial_number);
                break;
            case 'comic':
                $this->attachAttribute($item, 'Editorial', $itemData->publisher);
                $this->attachAttribute($item, 'Número de Ejemplar', $itemData->issue_number);
                $this->attachAttribute($item, 'Fecha de Portada', $itemData->cover_date);
                break;
            case 'watch':
                $this->attachAttribute($item, 'Marca', $itemData->brand);
                $this->attachAttribute($item, 'Modelo', $itemData->model);
                $this->attachAttribute($item, 'Material', $itemData->material);
                break;
            case 'stamp':
                $this->attachAttribute($item, 'Valor Facial', $itemData->face_value);
                break;
            case 'book':
                $this->attachAttribute($item, 'Autor', $itemData->author);
                $this->attachAttribute($item, 'Editorial', $itemData->publisher);
                $this->attachAttribute($item, 'ISBN', $itemData->isbn);
                break;
            case 'art':
                $this->attachAttribute($item, 'Artista', $itemData->artist);
                $this->attachAttribute($item, 'Dimensiones', $itemData->dimensions);
                $this->attachAttribute($item, 'Material', $itemData->material);
                break;
        }
    }

    private function attachAttribute(Item $item, string $attributeKey, mixed $value): void
    {
        if (empty($value)) {
            return;
        }
        $attribute = $this->attributes->get(strtolower(str_replace(' ', '_', $attributeKey)));
        if ($attribute) {
            $item->attributes()->attach($attribute->id, ['value' => $value]);
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
