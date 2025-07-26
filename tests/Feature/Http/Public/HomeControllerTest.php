<?php

// tests/Feature/Http/Public/HomeControllerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_cannot_access_the_home_dashboard(): void
    {
        // Act: Attempt to access the /home route as a guest
        $response = $this->get(route('home'));

        // Assert: The user should be redirected to the login page
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function an_authenticated_user_can_see_their_own_orders(): void
    {
        // Arrange: Create a user and an order specifically for them
        $user = User::factory()->create();
        $orderForUser = Order::factory()->create(['user_id' => $user->id]);

        // Act: Log in as the user and visit the home page
        $response = $this->actingAs($user)->get(route('home'));

        // Assert: The page loads and shows the user's order
        $response->assertStatus(200);
        $response->assertSee($orderForUser->order_number);
    }

    #[Test]
    public function an_authenticated_user_cannot_see_orders_from_other_users(): void
    {
        // Arrange: Create two different users and an order for the second user
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $orderForOtherUser = Order::factory()->create(['user_id' => $otherUser->id]);

        // Act: Log in as the first user and visit the home page
        $response = $this->actingAs($user)->get(route('home'));

        // Assert: The page should not contain the other user's order number
        $response->assertStatus(200);
        $response->assertDontSee($orderForOtherUser->order_number);
    }

    #[Test]
    public function an_authenticated_user_with_no_orders_sees_an_empty_message(): void
    {
        // Arrange: Create a user with no orders
        $user = User::factory()->create();

        // Act: Log in as this user and visit the home page
        $response = $this->actingAs($user)->get(route('home'));

        // Assert: The page should show the "no orders" message
        $response->assertStatus(200);
        $response->assertSee(__('You haven\'t placed any orders yet.'));
    }
}
