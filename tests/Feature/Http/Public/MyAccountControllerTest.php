<?php

// tests/Feature/Http/Public/MyAccountControllerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MyAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_cannot_access_the_my_account_page(): void
    {
        $response = $this->get(route('my-account'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function an_authenticated_user_can_see_their_own_orders_on_my_account_page(): void
    {
        $user = User::factory()->create();
        $orderForUser = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('my-account'));

        $response->assertStatus(200);
        $response->assertViewIs('public.my-account');
        $response->assertSee($orderForUser->order_number);
    }

    #[Test]
    public function an_authenticated_user_cannot_see_orders_from_other_users_on_my_account_page(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $orderForOtherUser = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('my-account'));

        $response->assertStatus(200);
        $response->assertDontSee($orderForOtherUser->order_number);
    }
}
