<?php

// tests/Feature/Http/Public/BladeStateSnapshotTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class BladeStateSnapshotTest extends TestCase
{
    use MatchesSnapshots, RefreshDatabase;

    #[Test]
    public function it_matches_the_marketplace_with_no_results_snapshot(): void
    {
        // No items created, so the marketplace should be empty.
        $response = $this->get(route('public.items.index'));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubSnapshot($response->content()));
    }

    #[Test]
    public function it_matches_the_empty_cart_page_snapshot(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubSnapshot($response->content()));
    }

    #[Test]
    public function it_matches_the_cart_page_with_items_snapshot(): void
    {
        // Arrange: Add some items to the cart.
        $item1 = Item::factory()->create(['status' => 'for_sale', 'name' => 'First Test Item']);
        $item2 = Item::factory()->create(['status' => 'for_sale', 'name' => 'Second Test Item']);

        session()->put('cart', [
            $item1->id => ['quantity' => 1],
            $item2->id => ['quantity' => 2],
        ]);

        $response = $this->get(route('cart.index'));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubSnapshot($response->content()));
    }

    #[Test]
    public function it_matches_the_empty_my_orders_page_snapshot(): void
    {
        $user = User::factory()->has(Customer::factory())->create();

        $response = $this->actingAs($user)->get(route('my-account.orders'));
        $response->assertOk();

        $this->assertMatchesSnapshot($this->scrubSnapshot($response->content()));
    }
}
