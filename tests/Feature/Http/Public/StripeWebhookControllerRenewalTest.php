<?php

// tests/Feature/Http/Public/StripeWebhookControllerRenewalTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionCancellationScheduledMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionPaymentFailedMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionReactivatedMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionRenewedMail;
use Numista\Collection\UI\Public\Controllers\StripeWebhookController;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripeWebhookControllerRenewalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renews_a_subscription_and_sends_email_on_invoice_paid(): void
    {
        Mail::fake();
        $tenant = Tenant::factory()->create(['stripe_customer_id' => 'cus_123']);
        $user = User::factory()->admin()->create();
        $tenant->users()->attach($user);

        $fakeInvoice = (object) [
            'customer' => 'cus_123',
            'billing_reason' => 'subscription_cycle',
            'lines' => (object) ['data' => [(object) ['period' => (object) ['end' => Carbon::now()->addMonth()->timestamp]]]],
        ];

        $webhookControllerMock = $this->partialMock(StripeWebhookController::class, fn ($mock) => $mock->shouldReceive('handleInvoicePaid')->once()->passthru());
        $webhookControllerMock->handleInvoicePaid($fakeInvoice);

        $this->assertEquals('active', $tenant->fresh()->subscription_status);
        Mail::assertQueued(SubscriptionRenewedMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    #[Test]
    public function it_deactivates_a_subscription_and_sends_email_on_invoice_payment_failed(): void
    {
        Mail::fake();
        $tenant = Tenant::factory()->create(['stripe_customer_id' => 'cus_456']);
        $user = User::factory()->admin()->create();
        $tenant->users()->attach($user);

        $fakeInvoice = (object) ['customer' => 'cus_456'];

        $webhookControllerMock = $this->partialMock(StripeWebhookController::class, fn ($mock) => $mock->shouldReceive('handleInvoicePaymentFailed')->once()->passthru());
        $webhookControllerMock->handleInvoicePaymentFailed($fakeInvoice);

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'subscription_status' => 'inactive']);
        Mail::assertQueued(SubscriptionPaymentFailedMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    #[Test]
    public function it_schedules_cancellation_and_sends_email_when_subscription_is_canceled_at_period_end(): void
    {
        Mail::fake();
        $tenant = Tenant::factory()->create(['stripe_subscription_id' => 'sub_123', 'subscription_status' => 'active']);
        $user = User::factory()->admin()->create();
        $tenant->users()->attach($user);
        $periodEndDate = now()->addMonth();

        // THE FIX: The fake object now includes the 'cancel_at' property, which is what the controller expects.
        $fakeSubscriptionObject = (object) [
            'id' => 'sub_123',
            'status' => 'active',
            'cancel_at_period_end' => true,
            'cancel_at' => $periodEndDate->timestamp, // This was the missing piece.
        ];

        $webhookControllerMock = $this->partialMock(StripeWebhookController::class, fn ($mock) => $mock->shouldReceive('handleSubscriptionUpdated')->once()->passthru());
        $webhookControllerMock->handleSubscriptionUpdated($fakeSubscriptionObject);

        $tenant->refresh();
        $this->assertEquals('active', $tenant->subscription_status);
        $this->assertEquals($periodEndDate->timestamp, $tenant->subscription_ends_at->timestamp);
        Mail::assertQueued(SubscriptionCancellationScheduledMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    #[Test]
    public function it_reactivates_a_subscription_and_sends_email_when_cancellation_is_undone(): void
    {
        Mail::fake();
        $tenant = Tenant::factory()->create(['stripe_subscription_id' => 'sub_456', 'subscription_status' => 'active', 'subscription_ends_at' => now()->addMonth()]);
        $user = User::factory()->admin()->create();
        $tenant->users()->attach($user);

        $fakeSubscriptionObject = (object) ['id' => 'sub_456', 'status' => 'active', 'cancel_at_period_end' => false, 'current_period_end' => now()->addMonth()->timestamp];

        $webhookControllerMock = $this->partialMock(StripeWebhookController::class, fn ($mock) => $mock->shouldReceive('handleSubscriptionUpdated')->once()->passthru());
        $webhookControllerMock->handleSubscriptionUpdated($fakeSubscriptionObject);

        $tenant->refresh();
        $this->assertNull($tenant->subscription_ends_at);
        Mail::assertQueued(SubscriptionReactivatedMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    #[Test]
    public function a_tenant_with_a_failed_payment_is_blocked_from_the_admin_panel(): void
    {
        $tenant = Tenant::factory()->create(['subscription_status' => 'inactive']);
        $user = User::factory()->admin()->create();
        $user->tenants()->attach($tenant);

        $response = $this->actingAs($user)->get(route('filament.admin.pages.dashboard', ['tenant' => $tenant]));

        $response->assertRedirect(route('subscription.create', $tenant));
    }

    #[Test]
    public function it_handles_the_subscription_deleted_webhook(): void
    {
        // Arrange
        $tenant = Tenant::factory()->create([
            'stripe_subscription_id' => 'sub_789',
            'subscription_status' => 'active',
        ]);

        // This is the fake payload Stripe sends for an immediate deletion.
        $fakeSubscriptionObject = (object) [
            'id' => 'sub_789',
        ];

        // Arrange: Mock our controller.
        $webhookControllerMock = $this->partialMock(StripeWebhookController::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('handleSubscriptionDeleted')->once()->passthru();
        });

        // Act: Call the handler.
        $webhookControllerMock->handleSubscriptionDeleted($fakeSubscriptionObject);

        // Assert: Check that the subscription is marked as 'canceled'.
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'subscription_status' => 'canceled',
            'subscription_ends_at' => null, // Ensure the end date is cleared.
        ]);
    }
}
