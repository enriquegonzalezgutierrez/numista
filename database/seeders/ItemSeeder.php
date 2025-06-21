<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Find the tenant ---
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (!$tenant) {
            $this->command->warn('Tenant "coleccion-numista" not found. Skipping ItemSeeder.');
            return;
        }

        // --- 2. Define the available image paths ---
        // These paths are relative to the 'tenants' disk root.
        // The structure matches what our FileUpload component creates.
        $imagePaths = [
            'tenant-1/item-images/Billete-Vintage.png',
            'tenant-1/item-images/Cómic-Clásico.png',
            'tenant-1/item-images/Moneda-Antigua.png',
            'tenant-1/item-images/Reloj-de-Pulsera-Vintage.png',
            'tenant-1/item-images/Sello-Antiguo.png',
        ];

        // --- 3. Create items using factories ---
        $coins = Item::factory(10)->coin()->create(['tenant_id' => $tenant->id]);
        $banknotes = Item::factory(10)->banknote()->create(['tenant_id' => $tenant->id]);
        $comics = Item::factory(8)->comic()->create(['tenant_id' => $tenant->id]);

        // --- 4. Attach Categories (existing logic) ---
        $monedasCategory = Category::where('slug', 'monedas')->first();
        if ($monedasCategory) {
            $coins->each(fn($item) => $item->categories()->attach($monedasCategory->id));
        }

        $billetesCategory = Category::where('slug', 'billetes')->first();
        if ($billetesCategory) {
            $banknotes->each(fn($item) => $item->categories()->attach($billetesCategory->id));
        }

        $marvelCategory = Category::where('slug', 'marvel')->first();
        if ($marvelCategory) {
            $comics->each(fn($item) => $item->categories()->attach($marvelCategory->id));
        }

        // --- 5. NEW: Attach random images to ALL created items ---
        $allItems = $coins->concat($banknotes)->concat($comics);

        $allItems->each(function (Item $item) use ($imagePaths) {
            // For each item, create 1 to 3 associated image records
            $numberOfImages = rand(1, 3);

            for ($i = 0; $i < $numberOfImages; $i++) {
                // Get a random image path from our list
                $randomImagePath = $imagePaths[array_rand($imagePaths)];

                // Create a record in the 'images' table
                $item->images()->create([
                    'path' => $randomImagePath,
                    'alt_text' => 'Imagen de prueba para ' . $item->name,
                    'order_column' => $i + 1,
                ]);
            }
        });

        $this->command->info('Item seeder finished and categories attached.');
    }
}
