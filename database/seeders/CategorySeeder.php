<?php

// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Tenant;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::truncate();
        $this->command->info('Cleaned previous categories.');

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found. Skipping CategorySeeder.');

            return;
        }

        foreach ($tenants as $tenant) {
            $this->command->info("Seeding categories for tenant: {$tenant->name}");
            $this->seedCategoriesForTenant($tenant);
        }
    }

    private function seedCategoriesForTenant(Tenant $tenant): void
    {
        $numismatica = $this->createCategory($tenant, 'Numismática y Filatelia');
        $monedas = $this->createCategory($tenant, 'Monedas', $numismatica->id);
        $this->createCategory($tenant, 'Moneda Española', $monedas->id);
        $this->createCategory($tenant, 'Sellos', $numismatica->id);

        $culturaPop = $this->createCategory($tenant, 'Cultura Pop y Juguetes');
        $comics = $this->createCategory($tenant, 'Cómics y Tebeos', $culturaPop->id);
        $this->createCategory($tenant, 'Marvel', $comics->id);

        $lujoVintage = $this->createCategory($tenant, 'Lujo y Vintage');
        $relojes = $this->createCategory($tenant, 'Relojes', $lujoVintage->id);
        $this->createCategory($tenant, 'Relojes de Pulsera', $relojes->id);
    }

    private function createCategory(Tenant $tenant, string $name, ?int $parentId = null): Category
    {
        return Category::create([
            'tenant_id' => $tenant->id, 'parent_id' => $parentId, 'name' => $name,
            'description' => fake()->paragraph(1), 'is_visible' => true,
        ]);
    }
}
