<?php

// database/seeders/CollectionSeeder.php

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
        // Clean previous collections and their images for all tenants
        Collection::query()->delete();
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            Storage::disk('tenants')->deleteDirectory("tenant-{$tenant->id}/collection-images");
        }
        $this->command->info('Cleaned previous collections and images.');

        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found. Skipping CollectionSeeder.');

            return;
        }

        foreach ($tenants as $tenant) {
            $this->command->info("Seeding collections for tenant: {$tenant->name}");
            $this->createCollection($tenant, 'Favoritos del Mes', 'Una selección de los mejores ítems de este mes.', 'collection-1.png');
            $this->createCollection($tenant, 'Lote para Venta de Verano', 'Ítems que se pondrán a la venta en un lote especial.', 'collection-2.png');
            $this->createCollection($tenant, 'Tesoros Personales', 'Artículos con un valor sentimental especial.', 'collection-3.png');
        }
    }

    /**
     * Helper function to create a collection and attach its placeholder image.
     */
    private function createCollection(Tenant $tenant, string $name, string $description, string $imageFilename): void
    {
        $collection = Collection::factory()->create([
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
        $sourceDir = database_path('seeders/placeholders');
        $disk = Storage::disk('tenants');
        $targetDirectory = "tenant-{$tenantId}/collection-images";
        $disk->makeDirectory($targetDirectory);

        $sourcePath = "{$sourceDir}/{$filename}";
        if (! File::exists($sourcePath)) {
            $this->command->warn("Placeholder image not found: {$filename}");
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
