<?php

// config/stripe.php

/**
 * This configuration file stores the API keys for Stripe.
 * Using a config file is a best practice in Laravel, allowing you to easily
 * access these values with config('stripe.key') and config('stripe.secret'),
 * and it also enables config caching for better performance in production.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key. These values are set in your
    | .env file and are used to authenticate with the Stripe API.
    |
    */
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
];
