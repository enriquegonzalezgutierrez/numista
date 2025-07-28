<?php

// tests/Feature/Http/Public/OrderControllerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_view_their_own_order_details(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('public.orders.show');
        $response->assertSee($order->order_number);
    }

    #[Test]
    public function a_user_cannot_view_another_users_order_details(): void
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $orderForUserTwo = Order::factory()->create(['user_id' => $userTwo->id]);

        $response = $this->actingAs($userOne)->get(route('orders.show', $orderForUserTwo));

        $response->assertStatus(403);
    }

    #[Test]
    public function a_guest_cannot_view_any_order_details(): void
    {
        $order = Order::factory()->create();

        $response = $this->get(route('orders.show', $order));

        $response->assertRedirect(route('login'));
    }
}
