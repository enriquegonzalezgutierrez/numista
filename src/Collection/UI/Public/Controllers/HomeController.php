<?php

// src/Collection/UI/Public/Controllers/HomeController.php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// THE FIX: Extend the base routing Controller from the framework
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // This will now work because Illuminate\Routing\Controller has the middleware method
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $orders = $user->orders()
            ->with('items.item')
            ->latest()
            ->paginate(10);

        return view('public.home', compact('orders'));
    }
}
