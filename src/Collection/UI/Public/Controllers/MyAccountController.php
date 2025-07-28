<?php

// src/Collection/UI/Public/Controllers/MyAccountController.php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyAccountController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard (My Account page).
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $orders = $user->orders()
            ->with('items.item')
            ->latest()
            ->paginate(10);

        // THE FIX: Return the new, consistently named view.
        return view('public.my-account', compact('orders'));
    }
}
