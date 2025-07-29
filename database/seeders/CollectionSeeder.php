<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Tenant;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (! $tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping CollectionSeeder.');

            return;
        }

        // Clean previous collections and their images for this tenant
        $collections = Collection::where('tenant_id', $tenant->id)->get();
        foreach ($collections as $collection) {
            $collection->image()->delete();
            $collection->delete();
        }
        Storage::disk('tenants')->deleteDirectory("tenant-{$tenant->id}/collection-images");
        $this->command->info('Cleaned previous collections and images for the tenant.');

        // THE FIX: Use simple, reliable filenames for placeholders
        $this->createCollection($tenant, 'Favoritos del Mes', 'Una selección de los mejores ítems de este mes.', 'collection-1.png');
        $this->createCollection($tenant, 'Lote para Venta de Verano', 'Ítems que se pondrán a la venta en un lote especial.', 'collection-2.png');
        $this->createCollection($tenant, 'Tesoros Personales', 'Artículos con un valor sentimental especial.', 'collection-3.png');

        $this->command->info('Collection seeder finished.');
    }

    /**
     * Helper function to create a collection and attach its placeholder image.
     */
    private function createCollection(Tenant $tenant, string $name, string $description, string $imageFilename): void
    {
        $collection = Collection::create([
            'tenant_id' => $tenant->id,
            'name' => $name,
            'description' => $description,
        ]);

        $imagePath = $this->copyPlaceholderImage($imageFilename, $tenant->id);

        if ($imagePath) {
            $collection->image()->create([
                'path' => $imagePath,
                'alt_text' => 'Imagen para la colección '.$name,
            ]);
        }
    }

    /**
     * Helper to copy an image from the seeder placeholders to the tenant's storage.
     */
    private function copyPlaceholderImage(string $filename, int $tenantId): ?string
    {
        // THE FIX: Point to the unified placeholders directory
        $sourceDir = database_path('seeders/placeholders');
        $disk = Storage::disk('tenants');
        $targetDirectory = "tenant-{$tenantId}/collection-images";
        $disk->makeDirectory($targetDirectory);

        $sourcePath = "{$sourceDir}/{$filename}";
        if (! File::exists($sourcePath)) {
            $this->command->warn("Placeholder image not found: {$filename}");
            // Fallback to a generic image if the specific one is missing
            $sourcePath = "{$sourceDir}/object.png";
            if (! File::exists($sourcePath)) {
                return null;
            }
        }

        $newFilename = uniqid().'-'.$filename;
        $newPath = "{$targetDirectory}/{$newFilename}";

        $disk->put($newPath, File::get($sourcePath));

        return $newPath;
    }
}
