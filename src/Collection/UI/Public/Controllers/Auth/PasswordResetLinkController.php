<?php

namespace Numista\Collection\UI\Public\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Numista\Collection\Infrastructure\Mail\Auth\ResetPasswordMail;

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

        /** @var User $user */
        $user = User::where('email', $request->email)->first();

        // This line is functionally correct.
        $token = Password::broker()->createToken($user);

        // We should still use the queue for sending emails.
        Mail::to($user)->queue(new ResetPasswordMail($token, $user->email));

        return back()->with('status', __(Password::RESET_LINK_SENT));
    }
}
