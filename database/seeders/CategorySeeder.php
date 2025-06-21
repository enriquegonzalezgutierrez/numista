<?php

// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Tenant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (!$tenant) {
            $this->command->warn('Default tenant "coleccion-numista" not found. Skipping CategorySeeder.');
            return;
        }

        // --- Create Parent Categories ---
        $numismatica = $this->createCategory($tenant, 'Numismática');
        $comics = $this->createCategory($tenant, 'Cómics y Tebeos');
        $filatelia = $this->createCategory($tenant, 'Filatelia');
        $arte = $this->createCategory($tenant, 'Arte');
        $relojes = $this->createCategory($tenant, 'Relojes y Joyería');

        // --- Create Child Categories ---
        $this->createCategory($tenant, 'Monedas', $numismatica->id);
        $this->createCategory($tenant, 'Billetes', $numismatica->id);
        $this->createCategory($tenant, 'Medallas', $numismatica->id);

        $this->createCategory($tenant, 'Marvel', $comics->id);
        $this->createCategory($tenant, 'DC Comics', $comics->id);
        $this->createCategory($tenant, 'Manga', $comics->id);

        $this->createCategory($tenant, 'Sellos', $filatelia->id);
        $this->createCategory($tenant, 'Postales', $filatelia->id);

        $this->command->info('Category seeder finished.');
    }

    /**
     * Helper function to create a category consistently.
     *
     * @param Tenant $tenant
     * @param string $name
     * @param int|null $parentId
     * @return Category
     */
    private function createCategory(Tenant $tenant, string $name, ?int $parentId = null): Category
    {
        return Category::create([
            'tenant_id' => $tenant->id,
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(2),
            'is_visible' => true,
        ]);
    }
}
