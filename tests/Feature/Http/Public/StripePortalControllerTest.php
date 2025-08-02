<?php

// tests/Feature/Http/Public/StripePortalControllerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Stripe\BillingPortal\Session as StripeBillingPortalSession;
use Tests\TestCase;

class StripePortalControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_guest_is_redirected_to_the_login_page(): void
    {
        $this->get(route('my-account.subscription.manage'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function a_tenant_admin_is_redirected_to_the_stripe_customer_portal(): void
    {
        // Arrange: Create a tenant with an active subscription and a Stripe customer ID.
        $tenant = Tenant::factory()->create([
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_123456789',
        ]);
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $user->tenants()->attach($tenant);

        // Arrange: Mock the Stripe SDK. We don't want to make a real API call.
        // We are only testing that OUR controller tries to create a session and redirects.
        $this->mock('alias:'.StripeBillingPortalSession::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('create')
                ->once() // Expect the 'create' method to be called exactly once.
                ->andReturn((object) [
                    'url' => 'https://billing.stripe.com/p/session/test_session_id', // Return a dummy Stripe URL.
                ]);
        });

        // Act & Assert: When the user hits our endpoint...
        $this->actingAs($user)
            ->get(route('my-account.subscription.manage'))
            ->assertRedirect('https://billing.stripe.com/p/session/test_session_id'); // ...they should be redirected to the URL Stripe gives us.
    }

    #[Test]
    public function it_returns_a_404_if_the_tenant_does_not_have_a_stripe_customer_id(): void
    {
        // Arrange: Create a tenant WITHOUT a stripe_customer_id.
        $tenant = Tenant::factory()->create(['stripe_customer_id' => null]);
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $user->tenants()->attach($tenant);

        // Act & Assert
        $this->actingAs($user)
            ->get(route('my-account.subscription.manage'))
            ->assertNotFound(); // The controller should trigger an abort(404).
    }
}
