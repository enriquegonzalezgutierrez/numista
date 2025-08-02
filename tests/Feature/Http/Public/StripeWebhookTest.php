<?php

// tests/Feature/Http/Public/StripeWebhookTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Numista\Collection\Domain\Events\SubscriptionActivated;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Public\Controllers\StripeWebhookController; // THE FIX: Import the controller
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_handles_the_checkout_session_completed_webhook_successfully(): void
    {
        Event::fake();

        $stripeCustomerId = 'cus_12345';
        $stripeSubscriptionId = 'sub_67890';
        $tenant = Tenant::factory()->create(['stripe_customer_id' => $stripeCustomerId]);
        $user = User::factory()->admin()->create();
        $tenant->users()->attach($user);

        // THE FIX: Create a mock of our own controller.
        $webhookControllerMock = $this->partialMock(StripeWebhookController::class, function ($mock) {
            // We tell the mock that it should expect a call to the 'handleCheckoutSessionCompleted' method.
            // By using a partial mock, the actual logic inside the method will run.
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('handleCheckoutSessionCompleted')->once()->passthru();
        });

        // Create the fake Stripe session object that the method expects.
        $fakeSession = (object) [
            'customer' => $stripeCustomerId,
            'subscription' => $stripeSubscriptionId,
        ];

        // Act: Directly call the method on our mocked controller instance.
        $webhookControllerMock->handleCheckoutSessionCompleted($fakeSession);

        // Assert: The tenant's status and subscription ID have been updated in the database.
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'subscription_status' => 'active',
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);

        // Assert: The SubscriptionActivated event was dispatched.
        Event::assertDispatched(SubscriptionActivated::class, function ($event) use ($tenant) {
            return $event->tenant->id === $tenant->id;
        });
    }
}
