<?php

// src/Collection/UI/Public/Controllers/Auth/TenantRegistrationController.php

namespace Numista\Collection\UI\Public\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered; // THE FIX: Import the Registered event class
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Tenant;

class TenantRegistrationController extends Controller
{
    /**
     * Display the tenant registration view.
     */
    public function create(): View
    {
        return view('auth.register-seller');
    }

    /**
     * Handle an incoming tenant registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tenant_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Use a transaction to ensure both user and tenant are created, or neither.
        $tenant = DB::transaction(function () use ($request) {
            // Create the user as an admin
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => true,
            ]);

            // Create the tenant
            $tenant = Tenant::create([
                'name' => $request->tenant_name,
                'slug' => Str::slug($request->tenant_name),
            ]);

            // Attach the new user to the new tenant
            $tenant->users()->attach($user);

            // THE FIX: Manually dispatch the Registered event.
            // This will trigger any listeners, like our SendWelcomeEmailToNewTenant.
            event(new Registered($user));

            Auth::login($user);

            return $tenant;
        });

        // Redirect to the subscription page for the newly created tenant
        return redirect()->route('subscription.create', ['tenant' => $tenant]);
    }
}
