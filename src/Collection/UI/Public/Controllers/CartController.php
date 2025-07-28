<?php

// src/Collection/UI/Public/Controllers/CartController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Item;

class CartController extends Controller
{
    /**
     * Display the cart contents.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $items = Item::with('images')->whereIn('id', array_keys($cart))->get();
        $total = 0;

        foreach ($items as $item) {
            $total += $item->sale_price * $cart[$item->id]['quantity'];
        }

        return view('public.cart.index', compact('items', 'cart', 'total'));
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$item->id])) {
            // Item already in cart, increment quantity
            $cart[$item->id]['quantity']++;
        } else {
            // Add new item to cart
            $cart[$item->id] = [
                'name' => $item->name,
                'quantity' => 1,
                'price' => $item->sale_price,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', __('public.cart_add_success'));
    }

    /**
     * Update an item's quantity in the cart.
     */
    public function update(Request $request, Item $item)
    {
        $cart = session()->get('cart');

        if (isset($cart[$item->id]) && $request->quantity) {
            $cart[$item->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', __('public.cart_update_success'));
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Item $item)
    {
        $cart = session()->get('cart');

        if (isset($cart[$item->id])) {
            unset($cart[$item->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', __('public.cart_remove_success'));
    }
}
