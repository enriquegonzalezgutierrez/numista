<?php

// src/Collection/UI/Public/Controllers/Auth/AuthenticatedSessionController.php

namespace Numista\Collection\UI\Public\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // It's good practice to import the User model.

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Clear the cart upon successful login to prevent stale data from a previous session.
        $request->session()->forget('cart');

        /** @var User|null $user */
        $user = Auth::user();

        // This defensive check is good practice, although unlikely to be hit
        // after a successful login attempt.
        if (! $user) {
            return redirect('/');
        }

        if ($user->is_admin) {
            $tenant = $user->tenants()->first();

            // Case 1: The admin user has no tenants yet. Redirect to create one.
            if (! $tenant) {
                return redirect()->route('filament.admin.tenant-registration');
            }

            // Case 2: The admin has a tenant, but the tenant does not have an active subscription.
            // Force them to the subscription page.
            if (! $tenant->hasActiveSubscription()) {
                return redirect()->route('subscription.create', ['tenant' => $tenant]);
            }

            // Case 3: The admin has a tenant and an active subscription. Proceed to the dashboard.
            return redirect()->intended(route('filament.admin.pages.dashboard', ['tenant' => $tenant]));
        }

        // Default case for non-admin users (customers).
        return redirect()->intended(route('public.items.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
