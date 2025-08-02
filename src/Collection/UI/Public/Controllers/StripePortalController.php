<?php

// src/Collection/UI/Public/Controllers/StripePortalController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Stripe;

class StripePortalController extends Controller
{
    /**
     * Redirect the authenticated tenant user to the Stripe Customer Portal.
     */
    public function redirectToPortal(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // FIX: Get the tenant from the authenticated user, not from Filament's global state.
        // An admin user in this system is expected to belong to one tenant.
        $tenant = $user->tenants()->first();

        // Ensure a tenant is active and has a Stripe customer ID
        if (! $tenant || ! $tenant->stripe_customer_id) {
            abort(404, __('public.error_tenant_not_found'));
        }

        Stripe::setApiKey(config('stripe.secret'));

        // The return URL is where the user will be sent back to after
        // they are done managing their subscription on the Stripe portal.
        $returnUrl = route('filament.admin.pages.dashboard', ['tenant' => $tenant]);

        $portalSession = \Stripe\BillingPortal\Session::create([
            'customer' => $tenant->stripe_customer_id,
            'return_url' => $returnUrl,
        ]);

        return redirect($portalSession->url);
    }
}
