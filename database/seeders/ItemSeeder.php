<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // --- 1. Find the tenant ---
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (!$tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping ItemSeeder.');
            return;
        }

        // Clean previous items for this tenant to avoid duplicates on re-seed
        Item::where('tenant_id', $tenant->id)->get()->each(fn ($item) => $item->delete());

        // --- 2. Define the available image paths ---
        $imagePaths = [
            'tenant-1/item-images/Billete-Vintage.png',
            'tenant-1/item-images/Cómic-Clásico.png',
            'tenant-1/item-images/Moneda-Antigua.png',
            'tenant-1/item-images/Reloj-de-Pulsera-Vintage.png',
            'tenant-1/item-images/Sello-Antiguo.png',
        ];

        // --- 3. Create items for each category ---
        $this->createItemsForCategory($tenant, 'moneda-espanola', 'coin', 5);
        $this->createItemsForCategory($tenant, 'marvel', 'comic', 8);
        $this->createItemsForCategory($tenant, 'billetes', 'banknote', 6);
        $this->createItemsForCategory($tenant, 'relojes-de-pulsera', 'watch', 4);
        $this->createItemsForCategory($tenant, 'sellos', 'stamp', 10);
        $this->createItemsForCategory($tenant, 'libros-y-manuscritos', 'book', 3);

        // --- 4. Attach random images to all created items ---
        $allItems = Item::where('tenant_id', $tenant->id)->get();
        $this->attachImagesToItems($allItems, $imagePaths);

        $this->command->info('Item seeder finished. ' . $allItems->count() . ' items created with categories and images.');
    }

    /**
     * Helper function to create items and attach them to a specific category.
     *
     * @param Tenant $tenant
     * @param string $categorySlug
     * @param string $itemType
     * @param int $count
     * @return void
     */
    private function createItemsForCategory(Tenant $tenant, string $categorySlug, string $itemType, int $count): void
    {
        $category = Category::where('tenant_id', $tenant->id)->where('slug', $categorySlug)->first();

        if (!$category) {
            $this->command->warn("Category with slug '{$categorySlug}' not found. Skipping item creation.");
            return;
        }

        // The factory automatically calls the correct state method (coin(), comic(), etc.)
        $items = Item::factory($count)->{$itemType}()->create(['tenant_id' => $tenant->id]);

        // Attach each created item to the category
        $items->each(fn (Item $item) => $item->categories()->attach($category->id));
    }

    /**
     * Helper function to attach a random number of images to a collection of items.
     *
     * @param Collection $items
     * @param array $imagePaths
     * @return void
     */
    private function attachImagesToItems(Collection $items, array $imagePaths): void
    {
        if (empty($imagePaths)) {
            return;
        }

        $items->each(function (Item $item) use ($imagePaths) {
            $numberOfImages = rand(1, 2); // 1 or 2 images per item

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