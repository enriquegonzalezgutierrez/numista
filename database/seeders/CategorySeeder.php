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

        // Clean previous categories for this tenant to avoid duplicates on re-seed
        Category::where('tenant_id', $tenant->id)->delete();

        // --- Create Parent Categories ---
        $numismatica = $this->createCategory($tenant, 'Numismática y Filatelia');
        $coleccionismoPapel = $this->createCategory($tenant, 'Coleccionismo de Papel');
        $arteAntiguedades = $this->createCategory($tenant, 'Arte y Antigüedades');
        $lujoVintage = $this->createCategory($tenant, 'Lujo y Vintage');
        $coleccionismoTecnico = $this->createCategory($tenant, 'Coleccionismo Técnico');
        $culturaPop = $this->createCategory($tenant, 'Cultura Pop y Juguetes');

        // --- Create Child Categories for "Numismática y Filatelia" ---
        $monedas = $this->createCategory($tenant, 'Monedas', $numismatica->id);
        $this->createCategory($tenant, 'Moneda Antigua', $monedas->id);
        $this->createCategory($tenant, 'Moneda Española', $monedas->id);
        $this->createCategory($tenant, 'Moneda Mundial', $monedas->id);

        $this->createCategory($tenant, 'Billetes', $numismatica->id);
        $this->createCategory($tenant, 'Medallas y Condecoraciones', $numismatica->id);
        $this->createCategory($tenant, 'Sellos', $numismatica->id);

        // --- Create Child Categories for "Coleccionismo de Papel" ---
        $this->createCategory($tenant, 'Libros y Manuscritos', $coleccionismoPapel->id);
        $this->createCategory($tenant, 'Postales Antiguas', $coleccionismoPapel->id);
        $this->createCategory($tenant, 'Fotografía Antigua', $coleccionismoPapel->id);
        $this->createCategory($tenant, 'Documentos Históricos', $coleccionismoPapel->id);

        // --- Create Child Categories for "Arte y Antigüedades" ---
        $this->createCategory($tenant, 'Pintura y Dibujo', $arteAntiguedades->id);
        $this->createCategory($tenant, 'Escultura', $arteAntiguedades->id);
        $this->createCategory($tenant, 'Mobiliario Antiguo', $arteAntiguedades->id);
        $this->createCategory($tenant, 'Artesanía', $arteAntiguedades->id);

        // --- Create Child Categories for "Lujo y Vintage" ---
        $relojes = $this->createCategory($tenant, 'Relojes', $lujoVintage->id);
        $this->createCategory($tenant, 'Relojes de Pulsera', $relojes->id);
        $this->createCategory($tenant, 'Relojes de Bolsillo', $relojes->id);

        $this->createCategory($tenant, 'Joyería', $lujoVintage->id);
        $this->createCategory($tenant, 'Instrumentos de Escritura', $lujoVintage->id);
        $this->createCategory($tenant, 'Moda Vintage', $lujoVintage->id);

        // --- Create Child Categories for "Coleccionismo Técnico" ---
        $this->createCategory($tenant, 'Cámaras Fotográficas', $coleccionismoTecnico->id);
        $this->createCategory($tenant, 'Radios y Gramófonos', $coleccionismoTecnico->id);
        $this->createCategory($tenant, 'Vehículos Clásicos', $coleccionismoTecnico->id);

        // --- Create Child Categories for "Cultura Pop" ---
        $comics = $this->createCategory($tenant, 'Cómics y Tebeos', $culturaPop->id);
        $this->createCategory($tenant, 'Marvel', $comics->id);
        $this->createCategory($tenant, 'DC Comics', $comics->id);
        $this->createCategory($tenant, 'Manga y Anime', $comics->id);

        $this->createCategory($tenant, 'Juguetes Antiguos', $culturaPop->id);
        $this->createCategory($tenant, 'Discos y Vinilos', $culturaPop->id);
        $this->createCategory($tenant, 'Memorabilia de Cine', $culturaPop->id);
        $this->createCategory($tenant, 'Memorabilia Deportiva', $culturaPop->id);

        $this->command->info('Category seeder finished with an extended structure.');
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
            // The slug is generated automatically by the CategoryObserver
            'description' => fake()->paragraph(1),
            'is_visible' => true,
        ]);
    }
}