<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as EloquentCollection;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (! $tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping ItemSeeder.');

            return;
        }

        // Clean previous items and images for the tenant
        Item::where('tenant_id', $tenant->id)->get()->each(fn ($item) => $item->delete());
        Storage::disk('tenants')->deleteDirectory("tenant-{$tenant->id}");
        $this->command->info('Cleaned previous items and images for the tenant.');

        // Create and get paths for realistic placeholder images.
        $imagePaths = $this->createPlaceholderImages($tenant->id);
        if (empty($imagePaths)) {
            $this->command->error('No placeholder images could be created. Aborting seeder.');

            return;
        }
        $this->command->info('Created '.count($imagePaths).' placeholder images.');

        $this->command->info('Creating a large volume of items...');

        // Create items
        $this->createItemsForCategory($tenant, 'moneda-espanola', 'coin', 100, true);
        $this->createItemsForCategory($tenant, 'marvel', 'comic', 50);
        $this->createItemsForCategory($tenant, 'relojes-de-pulsera', 'watch', 75, true);
        $this->createItemsForCategory($tenant, 'sellos', 'stamp', 150, true);
        $this->createItemsForCategory($tenant, 'libros-y-manuscritos', 'book', 75);
        $this->createItemsForCategory($tenant, 'arte-y-antiguedades', 'art', 50, true);

        // Process attachments after all items are created
        $allItems = Item::where('tenant_id', $tenant->id)->get();
        $allCollections = Collection::where('tenant_id', $tenant->id)->get();

        if ($allCollections->isNotEmpty() && $allItems->isNotEmpty()) {
            $this->attachItemsToCollections($allItems, $allCollections);
        }

        $this->attachImagesToItems($allItems, $imagePaths);

        $this->command->info("Item seeder finished. {$allItems->count()} items created with relations and images.");
    }

    private function createItemsForCategory(Tenant $tenant, string $categorySlug, string $itemType, int $count, bool $isForSale = false): void
    {
        $category = Category::where('slug', $categorySlug)->first();
        if (! $category) {
            $this->command->warn("Category '{$categorySlug}' not found. Skipping item creation.");

            return;
        }

        $items = Item::factory($count)->{$itemType}()->create([
            'tenant_id' => $tenant->id,
        ]);

        if ($isForSale) {
            // We must loop through each item to calculate its sale_price individually.
            foreach ($items as $item) {
                $item->status = 'for_sale';
                $item->sale_price = $item->purchase_price * fake()->randomFloat(2, 1.2, 2.5);
                $item->save();
            }
        }

        // Attach all items to the category at once.
        $category->items()->attach($items->pluck('id'));
    }

    private function attachItemsToCollections(EloquentCollection $items, EloquentCollection $collections): void
    {
        $collections->each(function (Collection $collection) use ($items) {
            $itemsToAttach = $items->random(min($items->count(), rand(15, 50)))->pluck('id');
            $collection->items()->sync($itemsToAttach);
        });
    }

    private function attachImagesToItems(EloquentCollection $items, array $imagePaths): void
    {
        if (empty($imagePaths)) {
            return;
        }

        foreach ($items as $item) {
            $numberOfImages = rand(3, 5);
            $selectedImagePaths = (new EloquentCollection($imagePaths))->random($numberOfImages)->all();

            foreach ($selectedImagePaths as $index => $path) {
                $item->images()->create([
                    'path' => $path,
                    'alt_text' => 'Image for '.$item->name,
                    'order_column' => $index + 1,
                ]);
            }
        }
    }

    /**
     * Copy real SVG placeholders into the tenant's storage directory.
     */
    private function createPlaceholderImages(int $tenantId): array
    {
        $disk = Storage::disk('tenants');
        $targetDirectory = "tenant-{$tenantId}/item-images";
        $disk->makeDirectory($targetDirectory);

        $sourceDir = base_path('database/seeders/placeholders');
        $placeholderFiles = scandir($sourceDir);

        $createdPaths = [];
        foreach ($placeholderFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $sourcePath = $sourceDir.'/'.$file;
                $newFilename = uniqid().'-'.$file; // Ensure unique filenames to avoid overwrites

                // Use Laravel's Storage facade which handles streams correctly
                $disk->put(
                    $targetDirectory.'/'.$newFilename,
                    file_get_contents($sourcePath)
                );

                $createdPaths[] = $targetDirectory.'/'.$newFilename;
            }
        }

        return $createdPaths;
    }
}
