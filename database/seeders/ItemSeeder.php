<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection as EloquentCollection;
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
        // 1. Find the tenant
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (! $tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping ItemSeeder.');

            return;
        }

        // 2. Clean previous items for this tenant to avoid duplicates
        Item::where('tenant_id', $tenant->id)->get()->each(fn($item) => $item->delete());

        // 3. Define the available image paths
        $imagePaths = [
            'tenant-1/item-images/Billete-Vintage.png',
            'tenant-1/item-images/Cómic-Clásico.png',
            'tenant-1/item-images/Moneda-Antigua.png',
            'tenant-1/item-images/Reloj-de-Pulsera-Vintage.png',
            'tenant-1/item-images/Sello-Antiguo.png',
        ];

        // 4. Create items for each category
        $this->createItemsForCategory($tenant, 'moneda-espanola', 'coin', 5);
        $this->createItemsForCategory($tenant, 'marvel', 'comic', 4);
        $this->createItemsForCategory($tenant, 'relojes-de-pulsera', 'watch', 4, true);
        $this->createItemsForCategory($tenant, 'sellos', 'stamp', 10, true);
        $this->createItemsForCategory($tenant, 'libros-y-manuscritos', 'book', 3, true);

        // --- 5. Get all created entities from the database ---
        $allItems = Item::where('tenant_id', $tenant->id)->get();
        $allCollections = Collection::where('tenant_id', $tenant->id)->get();

        // --- 6. Attach items to collections ---
        if ($allCollections->isNotEmpty()) {
            $this->attachItemsToCollections($allItems, $allCollections);
        }

        // --- 7. Attach images to all items ---
        $this->attachImagesToItems($allItems, $imagePaths);

        $this->command->info('Item seeder finished. ' . $allItems->count() . ' items created with relations.');
    }

    /**
     * Helper function to create items and attach them to a specific category.
     */
    private function createItemsForCategory(Tenant $tenant, string $categorySlug, string $itemType, int $count, bool $isForSale = false): void
    {
        $category = Category::where('tenant_id', $tenant->id)->where('slug', $categorySlug)->first();
        if (!$category) { /* ... */
            return;
        }

        // Prepare attributes to override the factory defaults
        $attributes = ['tenant_id' => $tenant->id];

        if ($isForSale) {
            $attributes['status'] = 'for_sale';
        }

        // Create items with the specific attributes
        $items = Item::factory($count)->{$itemType}()->create($attributes);

        // If for sale, we still need to calculate a sale_price
        if ($isForSale) {
            foreach ($items as $item) {
                $item->update([
                    'sale_price' => $item->purchase_price * fake()->randomFloat(2, 1.2, 2.5)
                ]);
            }
        }

        $items->each(fn(Item $item) => $item->categories()->attach($category->id));
    }

    /**
     * Helper function to attach random items to specific collections.
     */
    private function attachItemsToCollections(EloquentCollection $items, EloquentCollection $collections): void
    {
        // Attach 5 random items to the "Favoritos del Mes" collection
        $favorites = $collections->where('slug', 'favoritos-del-mes')->first();
        if ($favorites && $items->count() >= 5) {
            $favorites->items()->attach(
                $items->random(5)->pluck('id')
            );
        }

        // Attach 3 random items to the "Tesoros Personales" collection
        $treasures = $collections->where('slug', 'tesoros-personales')->first();
        if ($treasures && $items->count() >= 3) {
            $treasures->items()->attach(
                $items->random(3)->pluck('id')
            );
        }
    }

    /**
     * Helper function to attach a random number of images to a collection of items.
     */
    private function attachImagesToItems(EloquentCollection $items, array $imagePaths): void
    {
        if (empty($imagePaths)) {
            return;
        }

        $items->each(function (Item $item) use ($imagePaths) {
            $numberOfImages = rand(1, 2);

            for ($i = 0; $i < $numberOfImages; $i++) {
                $randomImagePath = $imagePaths[array_rand($imagePaths)];

                $item->images()->create([
                    'path' => $randomImagePath,
                    'alt_text' => 'Imagen de prueba para ' . $item->name,
                    'order_column' => $i + 1,
                ]);
            }
        });
    }
}
