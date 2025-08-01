<?php

// src/Collection/UI/Public/Controllers/SubscriptionController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User; // THE FIX: Import the User model
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Tenant;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    /**
     * Display the subscription plan selection page for a new tenant.
     */
    public function create(Tenant $tenant): View
    {
        /** @var User $user */
        $user = Auth::user();

        // THE FIX: Use a standard database relationship check instead of a Filament-specific method.
        // This checks if a record exists in the `tenant_user` pivot table for this user and tenant.
        if (! $user->tenants()->where('tenant_id', $tenant->id)->exists()) {
            abort(403);
        }

        return view('public.subscribe.create', [
            'tenant' => $tenant,
            'stripeKey' => config('stripe.key'),
            'priceMonthly' => config('stripe.prices.monthly'),
            'priceYearly' => config('stripe.prices.yearly'),
        ]);
    }

    /**
     * Create a Stripe Checkout session and redirect the user to the payment page.
     */
    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // THE FIX: Apply the same robust authorization check here.
        if (! $user->tenants()->where('tenant_id', $tenant->id)->exists()) {
            abort(403);
        }

        $request->validate(['price_id' => 'required|string']);
        $priceId = $request->input('price_id');

        Stripe::setApiKey(config('stripe.secret'));

        if (! $tenant->stripe_customer_id) {
            $customer = \Stripe\Customer::create([
                'name' => $tenant->name,
                'email' => $user->email,
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'tenant_slug' => $tenant->slug,
                ],
            ]);
            $tenant->update(['stripe_customer_id' => $customer->id]);
        }

        $checkoutSession = \Stripe\Checkout\Session::create([
            'customer' => $tenant->stripe_customer_id,
            'line_items' => [
                ['price' => $priceId, 'quantity' => 1],
            ],
            'mode' => 'subscription',
            'success_url' => route('filament.admin.pages.dashboard', ['tenant' => $tenant]),
            'cancel_url' => route('subscription.create', $tenant),
        ]);

        return redirect($checkoutSession->url);
    }
}
