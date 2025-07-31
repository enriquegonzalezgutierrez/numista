<?php

// tests/Feature/Domain/OrderRelationshipsTest.php

namespace Tests\Feature\Domain;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\OrderItem;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_order_can_have_multiple_items(): void
    {
        // THE FIX: Create a user and tenant first to associate with the order.
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();

        $order = Order::factory()
            ->has(OrderItem::factory()->count(3), 'items')
            ->create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
            ]);

        $this->assertCount(3, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items->first());
    }

    #[Test]
    public function an_order_belongs_to_a_customer_and_a_tenant(): void
    {
        // THE FIX: We still need to create a user and tenant for the factory.
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->assertNotNull($order->customer);
        $this->assertNotNull($order->tenant);
        $this->assertInstanceOf(\App\Models\User::class, $order->customer);
        $this->assertInstanceOf(\Numista\Collection\Domain\Models\Tenant::class, $order->tenant);
    }
}
