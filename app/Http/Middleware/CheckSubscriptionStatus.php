<?php

// app/Http/Middleware/CheckSubscriptionStatus.php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current tenant being accessed through Filament.
        $tenant = Filament::getTenant();

        // If a tenant is being accessed and it does NOT have an active subscription...
        if ($tenant && ! $tenant->hasActiveSubscription()) {
            // ...and we are NOT already on the subscription page (to avoid a redirect loop)...
            if (! $request->routeIs('subscription.create')) {
                // ...redirect the user to the subscription page for the current tenant.
                return redirect()->route('subscription.create', ['tenant' => $tenant]);
            }
        }

        // Otherwise, if the user is subscribed or there's no tenant context, allow the request to proceed.
        return $next($request);
    }
}
