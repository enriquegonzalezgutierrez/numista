<?php

// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::truncate();
        $this->command->info('Cleaned previous categories.');
        $this->command->info('Seeding global categories...');

        $this->seedCategories();
    }

    private function seedCategories(): void
    {
        // THE FIX: Logic no longer loops through tenants. It creates one global tree.
        $numismatica = $this->createCategory('Numismática y Filatelia');
        $monedas = $this->createCategory('Monedas', $numismatica->id);
        $this->createCategory('Moneda Española', $monedas->id);
        $this->createCategory('Sellos', $numismatica->id);

        $culturaPop = $this->createCategory('Cultura Pop y Juguetes');
        $comics = $this->createCategory('Cómics y Tebeos', $culturaPop->id);
        $this->createCategory('Marvel', $comics->id);

        $lujoVintage = $this->createCategory('Lujo y Vintage');
        $relojes = $this->createCategory('Relojes', $lujoVintage->id);
        $this->createCategory('Relojes de Pulsera', $relojes->id);
    }

    private function createCategory(string $name, ?int $parentId = null): Category
    {
        // THE FIX: Removed the tenant_id assignment.
        return Category::create([
            'parent_id' => $parentId,
            'name' => $name,
            'description' => fake()->paragraph(1),
            'is_visible' => true,
        ]);
    }
}
