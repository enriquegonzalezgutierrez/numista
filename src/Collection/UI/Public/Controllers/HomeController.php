<?php

// src/Collection/UI/Public/Controllers/HomeController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // This middleware ensures only logged-in users can access this page
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Eager load the items and their associated item for each order
        $orders = $user->orders()
            ->with('items.item')
            ->latest() // Show the most recent orders first
            ->paginate(10);

        return view('home', compact('orders'));
    }
}
