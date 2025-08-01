<?php

// src/Collection/UI/Public/Controllers/StripeWebhookController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Numista\Collection\Domain\Events\SubscriptionActivated;
use Numista\Collection\Domain\Models\Tenant;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Stripe.
     */
    public function handleWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            // Verify the event is genuinely from Stripe
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        // Handle the event based on its type
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;
                // You can add more cases here for other events like:
                // case 'customer.subscription.deleted':
                // case 'invoice.payment_failed':
                //     // Handle failed payments or cancellations
                //     break;
        }

        return response('Webhook Handled', 200);
    }

    /**
     * Handle the checkout.session.completed event.
     * This is triggered when a user successfully completes the Stripe Checkout page.
     */
    public function handleCheckoutSessionCompleted(object $session): void
    {
        $stripeCustomerId = $session->customer;
        $tenant = Tenant::where('stripe_customer_id', $stripeCustomerId)->first();

        if ($tenant && $tenant->subscription_status !== 'active') { // Process only if not already active
            $tenant->update([
                'subscription_status' => 'active',
                'stripe_subscription_id' => $session->subscription,
            ]);

            // THE FIX: Dispatch the event to trigger post-activation tasks like sending emails.
            SubscriptionActivated::dispatch($tenant);

            Log::info("Subscription activated for tenant ID: {$tenant->id}");
        } elseif (! $tenant) {
            Log::error("Webhook received for unknown Stripe customer ID: {$stripeCustomerId}");
        }
    }
}
