<?php

// database/seeders/ItemTypeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\ItemType;
use Numista\Collection\UI\Filament\ItemTypeManager;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemType::truncate();
        $this->command->info('Cleaned previous item types.');

        $manager = new ItemTypeManager;
        $types = $manager->getTypesForSelect();

        foreach (array_keys($types) as $typeName) {
            ItemType::create(['name' => $typeName]);
        }

        $this->command->info('Seeded item types successfully.');
    }
}
