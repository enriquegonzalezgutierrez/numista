<?php

// database/seeders/CollectionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        // Clean previous collections for this tenant
        Collection::where('tenant_id', $tenant->id)->delete();

        // Create some example collections
        $this->createCollection($tenant, 'Favoritos del Mes', 'Una selección de los mejores ítems de este mes.');
        $this->createCollection($tenant, 'Lote para Venta de Verano', 'Ítems que se pondrán a la venta en un lote especial.');
        $this->createCollection($tenant, 'Tesoros Personales', 'Artículos con un valor sentimental especial.');

        $this->command->info('Collection seeder finished.');
    }

    /**
     * Helper function to create a collection consistently.
     */
    private function createCollection(Tenant $tenant, string $name, string $description): void
    {
        Collection::create([
            'tenant_id' => $tenant->id,
            'name' => $name,
            // The slug is generated automatically by the CollectionObserver
            'description' => $description,
        ]);
    }
}
