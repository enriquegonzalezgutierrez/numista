<?php

namespace Tests\Feature\Http\Public;

use Database\Seeders\SnapshotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class AAABladeSnapshotTest extends TestCase
{
    use MatchesSnapshots, RefreshDatabase;

    /**
     * This method runs before each test in this class.
     * It seeds the predictable data that both tests will use.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SnapshotSeeder::class);
    }

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
        // We get the first item created by the seeder, which will always have id=1.
        $item = Item::find(1);
        $item->images()->create(['path' => 'test/image.jpg', 'alt_text' => 'Vista frontal de la moneda']);

        $response = $this->get(route('public.items.show', $item));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubCsrf($response->content()));
    }

    #[Test]
    public function it_matches_the_marketplace_index_page_snapshot(): void
    {
        // The data has already been created in the setUp() method.
        $response = $this->get(route('public.items.index'));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubCsrf($response->content()));
    }
}
