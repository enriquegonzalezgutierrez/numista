<?php

// config/stripe.php

return [
    /**
     * Stripe API Keys.
     * These keys are used to authenticate with the Stripe API.
     */
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),

    /**
     * THE FIX: Stripe Webhook Signing Secret.
     * This key is used to verify that incoming webhook requests are genuinely from Stripe.
     * You can get this from the `stripe listen` command output.
     */
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /**
     * Stripe Billing Price IDs.
     * These IDs correspond to the subscription plans (Prices) you have
     * created in your Stripe dashboard.
     */
    'prices' => [
        'monthly' => env('STRIPE_PRICE_ID_MONTHLY'),
        'yearly' => env('STRIPE_PRICE_ID_YEARLY'),
    ],
];
