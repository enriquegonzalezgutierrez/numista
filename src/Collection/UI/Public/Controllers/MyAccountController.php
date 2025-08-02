<?php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the account dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        return view('public.my-account.dashboard', compact('user'));
    }

    /**
     * Show the user's order history.
     */
    public function orders(): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $orders = $user->orders()
            ->with('items.item')
            ->latest()
            ->paginate(10);

        return view('public.my-account.orders', compact('orders'));
    }
}
