<?php

namespace Tests\Feature\Http\Public;

use Database\Seeders\SnapshotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class BladeSnapshotTest extends TestCase
{
    use MatchesSnapshots, RefreshDatabase;

    /**
     * Helper function to scrub all known dynamic CSRF tokens from HTML content.
     */
    private function scrubCsrf(string $content): string
    {
        // 1. Scrub the meta tag
        $content = preg_replace('/<meta name="csrf-token" content=".*">/', '<meta name="csrf-token" content="[FILTERED]">', $content);

        // 2. Scrub the hidden input field in forms
        $content = preg_replace('/<input type="hidden" name="_token" value=".*" autocomplete="off">/', '<input type="hidden" name="_token" value="[FILTERED]" autocomplete="off">', $content);

        // 3. Scrub the token in JavaScript fetch calls
        $content = preg_replace('/(\'X-CSRF-TOKEN\': \')([a-zA-Z0-9]+)(\',)/', '$1[FILTERED]$3', $content);

        return $content;
    }

    #[Test]
    public function it_matches_the_item_details_page_snapshot(): void
    {
        $tenant = Tenant::factory()->create(['name' => 'Colección de Prueba']);
        $category = Category::factory()->create(['name' => 'Monedas de Plata', 'tenant_id' => $tenant->id]);
        $attributeMaterial = Attribute::factory()->create(['name' => 'Material', 'tenant_id' => $tenant->id]);
        $attributeYear = Attribute::factory()->create(['name' => 'Año', 'tenant_id' => $tenant->id]);

        $item = Item::factory()
            ->hasAttached($category)
            ->hasAttached($attributeMaterial, ['value' => 'Plata'])
            ->hasAttached($attributeYear, ['value' => '1999'])
            ->hasImages(1, ['path' => 'test/image.jpg', 'alt_text' => 'Vista frontal de la moneda'])
            ->create([
                'tenant_id' => $tenant->id,
                'status' => 'for_sale',
                'name' => 'Moneda de Plata de 1999',
                'description' => 'Una hermosa moneda de plata de prueba.',
                'sale_price' => 123.45,
            ]);

        $response = $this->get(route('public.items.show', $item));
        $response->assertOk();

        // THE FIX: Use the comprehensive scrubber helper
        $this->assertMatchesSnapshot($this->scrubCsrf($response->content()));
    }

    #[Test]
    public function it_matches_the_marketplace_index_page_snapshot(): void
    {
        $this->seed(SnapshotSeeder::class);

        $response = $this->get(route('public.items.index'));
        $response->assertOk();

        // THE FIX: Use the comprehensive scrubber helper
        $this->assertMatchesSnapshot($this->scrubCsrf($response->content()));
    }
}
