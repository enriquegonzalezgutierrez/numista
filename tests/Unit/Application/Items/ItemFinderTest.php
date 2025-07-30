<?php

// tests/Unit/Application/Items/ItemFinderTest.php

namespace Tests\Unit\Application\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Application\Items\ItemFinder;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemFinderTest extends TestCase
{
    use RefreshDatabase;

    private ItemFinder $itemFinder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->itemFinder = new ItemFinder;
    }

    #[Test]
    public function it_filters_by_search_term_using_like_on_sqlite(): void
    {
        // ... (este test ya funcionaba y se queda igual)
        $item1 = Item::factory()->create(['name' => 'Moneda de Plata Antigua', 'status' => 'for_sale']);
        $item2 = Item::factory()->create(['name' => 'Reloj de Oro', 'status' => 'for_sale']);
        $item3 = Item::factory()->create(['name' => 'Sello Raro', 'description' => 'Un sello de plata conmemorativo.', 'status' => 'for_sale']);

        $paginator = $this->itemFinder->forMarketplace(['search' => 'Plata']);
        $results = $paginator->items();

        $this->assertCount(2, $results);
        $resultIds = collect($results)->pluck('id');
        $this->assertContains($item1->id, $resultIds);
        $this->assertContains($item3->id, $resultIds);
        $this->assertNotContains($item2->id, $resultIds);
    }

    #[Test]
    public function it_builds_a_full_text_search_query_when_driver_is_pgsql(): void
    {
        // Arrange: Mock the DB facade to return 'pgsql'.
        DB::shouldReceive('getDriverName')->andReturn('pgsql');

        // Arrange: Get a fresh query builder instance.
        $queryBuilder = Item::query();

        // Act: Apply the filters directly to the builder.
        $this->itemFinder->applyFilters($queryBuilder, ['search' => 'moneda rara']);

        // Assert: Check the generated SQL string for the PostgreSQL-specific syntax.
        $this->assertStringContainsString(
            "to_tsvector('spanish', name || ' ' || description) @@ to_tsquery('spanish', ?)",
            $queryBuilder->toSql(),
            "The query should use PostgreSQL's to_tsvector function."
        );

        // Assert: Check that the bindings are correct.
        $this->assertEquals(['moneda & rara'], $queryBuilder->getBindings());
    }
}
