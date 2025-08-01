<?php

// src/Collection/UI/Public/Controllers/StripeWebhookController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Events\SubscriptionActivated;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionCancellationScheduledMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionPaymentFailedMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionReactivatedMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionRenewedMail;
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
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException|SignatureVerificationException $e) {
            return response('Invalid signature or payload', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;
            case 'invoice.paid':
                $this->handleInvoicePaid($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
        }

        return response('Webhook Handled', 200);
    }

    /**
     * Handle the checkout.session.completed event.
     */
    protected function handleCheckoutSessionCompleted(object $session): void
    {
        $tenant = Tenant::where('stripe_customer_id', $session->customer)->first();

        if ($tenant && $tenant->subscription_status !== 'active') {
            $tenant->update([
                'subscription_status' => 'active',
                'stripe_subscription_id' => $session->subscription,
            ]);
            SubscriptionActivated::dispatch($tenant);
            Log::info("Subscription activated for tenant ID: {$tenant->id}");
        }
    }

    /**
     * Handle the invoice.paid event for subscription renewals.
     */
    public function handleInvoicePaid(object $invoice): void
    {
        // First check: Ignore invoices for new subscriptions.
        if ($invoice->billing_reason === 'subscription_create') {
            return;
        }

        $tenant = Tenant::where('stripe_customer_id', $invoice->customer)->first();
        if ($tenant) {
            // THE FINAL FIX: If a subscription is already scheduled for cancellation,
            // don't send a renewal email. The 'invoice.paid' event might just be
            // for accounting purposes related to the final billing cycle.
            if ($tenant->subscription_ends_at !== null) {
                Log::info("Ignoring invoice.paid for tenant {$tenant->id} because a cancellation is scheduled.");

                return;
            }

            $periodEnd = Carbon::createFromTimestamp($invoice->lines->data[0]->period->end);
            $tenant->update(['subscription_status' => 'active', 'subscription_ends_at' => $periodEnd]);

            /** @var User|null $user */
            $user = $tenant->users()->first();
            if ($user) {
                Mail::to($user->email)->queue(new SubscriptionRenewedMail($tenant, $user));
            }
            Log::info("Subscription successfully renewed for tenant ID: {$tenant->id}.");
        }
    }

    /**
     * Handle the invoice.payment_failed event.
     */
    public function handleInvoicePaymentFailed(object $invoice): void
    {
        $tenant = Tenant::where('stripe_customer_id', $invoice->customer)->first();
        if ($tenant) {
            $tenant->update(['subscription_status' => 'inactive']);

            /** @var User|null $user */
            $user = $tenant->users()->first();
            if ($user) {
                Mail::to($user->email)->queue(new SubscriptionPaymentFailedMail($tenant, $user));
            }
            Log::warning("Subscription payment failed for tenant ID: {$tenant->id}. Status set to inactive.");
        }
    }

    /**
     * Handle the customer.subscription.updated event.
     * This handles both cancellations at period end AND reactivations.
     */
    public function handleSubscriptionUpdated(object $subscription): void
    {
        $tenant = Tenant::where('stripe_subscription_id', $subscription->id)->first();

        if ($tenant) {
            /** @var User|null $user */
            $user = $tenant->users()->first();

            if ($subscription->cancel_at_period_end) {
                $endDate = Carbon::createFromTimestamp($subscription->cancel_at);

                // --- IDEMPOTENCY CHECK ---
                // If the cancellation date in our database already matches the one from the webhook,
                // it means we have already processed this event. Do nothing further.
                if ($tenant->subscription_ends_at && $tenant->subscription_ends_at->eq($endDate)) {
                    Log::info("Ignoring duplicate subscription cancellation webhook for tenant ID {$tenant->id}.");

                    return;
                }

                // If it's a new cancellation request, update the database and send the email.
                $tenant->update(['subscription_ends_at' => $endDate]);
                if ($user) {
                    Mail::to($user->email)->queue(new SubscriptionCancellationScheduledMail($tenant, $user, $endDate));
                }
                Log::info("Tenant ID {$tenant->id} subscription will be canceled on {$endDate->toDateString()}.");

            } else {
                // --- IDEMPOTENCY CHECK ---
                // If the subscription is already marked as not ending (null), do nothing.
                if ($tenant->subscription_ends_at === null) {
                    Log::info("Ignoring duplicate subscription reactivation webhook for tenant ID {$tenant->id}.");

                    return;
                }

                // If it's a new reactivation, update and send the email.
                $tenant->update(['subscription_ends_at' => null]);
                if ($user) {
                    Mail::to($user->email)->queue(new SubscriptionReactivatedMail($tenant, $user));
                }
                Log::info("Tenant ID {$tenant->id} subscription has been reactivated.");
            }
        }
    }

    /**
     * Handle the customer.subscription.deleted event for immediate cancellations.
     */
    public function handleSubscriptionDeleted(object $subscription): void
    {
        $tenant = Tenant::where('stripe_subscription_id', $subscription->id)->first();
        if ($tenant) {
            $tenant->update(['subscription_status' => 'canceled', 'subscription_ends_at' => null]);
            Log::info("Subscription canceled for tenant ID: {$tenant->id}. Status set to 'canceled'.");
        }
    }
}
