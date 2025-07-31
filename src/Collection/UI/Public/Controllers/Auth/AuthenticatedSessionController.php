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

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user->is_admin) {
            $firstTenant = $user->tenants()->first();

            if ($firstTenant) {
                return redirect()->intended(route('filament.admin.pages.dashboard', ['tenant' => $firstTenant]));
            } else {
                return redirect()->route('filament.admin.tenant-registration');
            }
        }

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
