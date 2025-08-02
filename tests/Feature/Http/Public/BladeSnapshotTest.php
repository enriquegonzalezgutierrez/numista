<?php

// tests/Feature/Http/Public/BladeSnapshotTest.php

namespace Tests\Feature\Http\Public;

use Database\Seeders\SnapshotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class BladeSnapshotTest extends TestCase
{
    use MatchesSnapshots, RefreshDatabase;

    /**
     * This method runs before each test in this class.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // THE FIX: We no longer need scout:flush here as it's handled in the parent TestCase.
        $this->seed(SnapshotSeeder::class);
    }

    #[Test]
    public function it_matches_the_item_details_page_snapshot(): void
    {
        // Get the first item created by the seeder.
        $item = Item::firstOrFail();
        $item->images()->create(['path' => 'test/image.jpg', 'alt_text' => 'Vista frontal de la moneda']);

        $response = $this->get(route('public.items.show', $item));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubSnapshot($response->content()));
    }

    #[Test]
    public function it_matches_the_marketplace_index_page_snapshot(): void
    {
        // The data has already been created in the setUp() method.
        $response = $this->get(route('public.items.index'));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubSnapshot($response->content()));
    }
}
