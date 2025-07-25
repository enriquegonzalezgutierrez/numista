<?php

// src/Collection/UI/Public/Controllers/Auth/AuthenticatedSessionController.php

namespace Numista\Collection\UI\Public\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // --- NEW LOGIC: Redirect based on user role ---
        if ($user->is_admin) {
            // If the user is an admin, redirect to their Filament panel.
            // We will try to redirect to their first available tenant.
            $firstTenant = $user->tenants()->first();

            if ($firstTenant) {
                return redirect()->intended(route('filament.admin.pages.dashboard', ['tenant' => $firstTenant]));
            } else {
                // If the admin has no tenants, redirect to the tenant creation page.
                return redirect()->route('filament.admin.tenant-registration');
            }
        }

        // If the user is a regular customer, redirect to the marketplace.
        return redirect()->intended(route('public.items.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
