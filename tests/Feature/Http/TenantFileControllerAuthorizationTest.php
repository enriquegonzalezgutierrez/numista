<?php

// tests/Feature/Http/TenantFileControllerAuthorizationTest.php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\OrderItem;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantFileControllerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('tenants');
    }

    #[Test]
    public function a_guest_can_access_an_image_of_a_publicly_browsable_item_for_sale(): void
    {
        $item = Item::factory()->create(['status' => 'for_sale']);
        $image = $item->images()->create(['path' => 'test.jpg']);
        Storage::disk('tenants')->put('test.jpg', 'content');

        $this->get(route('images.show', $image))->assertOk();
    }

    #[Test]
    public function a_guest_can_access_an_image_of_a_public_collection(): void
    {
        $collection = Collection::factory()->create();
        $image = $collection->image()->create(['path' => 'test.jpg']);
        Storage::disk('tenants')->put('test.jpg', 'content');

        $this->get(route('images.show', $image))->assertOk();
    }

    #[Test]
    public function a_guest_cannot_access_an_image_of_a_private_item_in_collection(): void
    {
        $item = Item::factory()->create(['status' => 'in_collection']);
        $image = $item->images()->create(['path' => 'test.jpg']);
        Storage::disk('tenants')->put('test.jpg', 'content');

        $this->get(route('images.show', $image))->assertForbidden();
    }

    #[Test]
    public function an_admin_can_access_an_image_of_a_private_item_in_their_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->admin()->create();
        $admin->tenants()->attach($tenant);
        $item = Item::factory()->create(['status' => 'in_collection', 'tenant_id' => $tenant->id]);
        $image = $item->images()->create(['path' => 'test.jpg']);
        Storage::disk('tenants')->put('test.jpg', 'content');

        $this->actingAs($admin)->get(route('images.show', $image))->assertOk();
    }

    #[Test]
    public function a_user_who_purchased_an_item_can_access_its_image_even_if_sold(): void
    {
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['status' => 'sold']);
        $order = Order::factory()->create(['user_id' => $buyer->id, 'tenant_id' => $item->tenant_id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'item_id' => $item->id]);

        $image = $item->images()->create(['path' => 'test.jpg']);
        Storage::disk('tenants')->put('test.jpg', 'content');

        $this->actingAs($buyer)->get(route('images.show', $image))->assertOk();
    }

    #[Test]
    public function a_random_user_cannot_access_an_image_of_a_sold_item_they_did_not_buy(): void
    {
        $randomUser = User::factory()->create();
        $item = Item::factory()->create(['status' => 'sold']);
        $image = $item->images()->create(['path' => 'test.jpg']);
        Storage::disk('tenants')->put('test.jpg', 'content');

        $this->actingAs($randomUser)->get(route('images.show', $image))->assertForbidden();
    }
}
