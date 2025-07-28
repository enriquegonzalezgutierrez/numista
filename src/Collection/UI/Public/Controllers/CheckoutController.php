<?php

// src/Collection/UI/Public/Controllers/CheckoutController.php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;

class CheckoutController extends Controller
{
    /**
     * Users must be authenticated to access any checkout functionality.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the checkout page.
     */
    public function create()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index')->with('error', 'Your cart is empty.');
        }

        $items = Item::with('images')->whereIn('id', array_keys($cart))->get();
        $total = $items->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);
        $user = Auth::user();

        return view('public.checkout.index', compact('items', 'cart', 'total', 'user'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index');
        }

        $request->validate([
            'shipping_address' => 'required|string|max:1000',
        ]);

        // Use a database transaction to ensure data integrity
        $order = DB::transaction(function () use ($user, $cart, $request) {
            $itemsInCart = Item::whereIn('id', array_keys($cart))->get();
            $total = $itemsInCart->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);

            /** @var \App\Models\User $user */
            $order = Order::create([
                'tenant_id' => $itemsInCart->first()->tenant_id, // Assume all items from same tenant
                'user_id' => $user->id,
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'total_amount' => $total,
                'status' => 'paid', // Assume payment is successful for now
                'shipping_address' => $request->shipping_address,
                'payment_method' => 'Placeholder',
                'payment_status' => 'successful',
            ]);

            foreach ($itemsInCart as $item) {
                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $cart[$item->id]['quantity'],
                    'price' => $item->sale_price,
                ]);
            }

            return $order;
        });

        // Clear the cart from the session
        $request->session()->forget('cart');

        return redirect()->route('checkout.success', $order);
    }

    /**
     * Show the order confirmation page.
     */
    public function success(Order $order)
    {
        // Security check: ensure the user is viewing their own success page
        if ($order->user_id !== Auth::id()) {
            // THE FIX: Redirect to the new 'my-account.orders' route
            return redirect()->route('my-account.orders');
        }

        return view('public.checkout.success', compact('order'));
    }
}
