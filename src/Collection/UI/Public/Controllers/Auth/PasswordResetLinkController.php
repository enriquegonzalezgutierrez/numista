<?php

namespace Numista\Collection\UI\Public\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Numista\Collection\UI\Public\Mail\Auth\ResetPasswordMail;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        $token = Password::broker()->createToken($user);

        Mail::to($user)->send(new ResetPasswordMail($token, $user->email));

        // Using the key from the passwords translation file for consistency
        return back()->with('status', __(Password::RESET_LINK_SENT));
    }
}
