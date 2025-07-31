<?php

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
    public function guests_are_redirected_from_account_pages(): void
    {
        $this->get(route('my-account.dashboard'))->assertRedirect(route('login'));
        $this->get(route('my-account.orders'))->assertRedirect(route('login'));
    }

    #[Test]
    public function an_authenticated_user_can_view_the_account_dashboard(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('my-account.dashboard'))
            ->assertStatus(200)
            ->assertViewIs('public.my-account.dashboard')
            ->assertViewHas('user', $user) // Verify controller passes correct data
            ->assertSee($user->name); // Simple check for the name
    }

    #[Test]
    public function an_authenticated_user_can_see_their_own_orders_on_orders_page(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $orderForUser = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('my-account.orders'))
            ->assertStatus(200)
            ->assertViewIs('public.my-account.orders')
            ->assertSee($orderForUser->order_number);
    }

    #[Test]
    public function an_authenticated_user_cannot_see_orders_from_other_users_on_orders_page(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $orderForOtherUser = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)->get(route('my-account.orders'))
            ->assertStatus(200)
            ->assertDontSee($orderForOtherUser->order_number);
    }
}
