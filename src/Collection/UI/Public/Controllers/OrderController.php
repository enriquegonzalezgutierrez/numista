<?php

// src/Collection/UI/Public/Controllers/OrderController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Order;

class OrderController extends Controller
{
    /**
     * Show the details of a specific order.
     */
    public function show(Order $order): View
    {
        // --- SECURITY CHECK ---
        // This ensures a user can only see their own orders.
        // If they try to access an order that is not theirs, they will get a 403 Forbidden error.
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Eager load the relationships needed for the view
        $order->load('items.item.images');

        return view('public.orders.show', compact('order'));
    }
}
