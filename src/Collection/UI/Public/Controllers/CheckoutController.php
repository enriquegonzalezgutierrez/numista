<?php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Numista\Collection\Application\Checkout\PlaceOrderService;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\UI\Public\Requests\StoreCheckoutRequest; // THE FIX: Use Form Request

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index')->with('error', 'Tu carrito está vacío.');
        }

        $items = Item::with('images')->whereIn('id', array_keys($cart))->get();
        $total = $items->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);
        $user = Auth::user();
        $addresses = $user->customer->addresses;

        $countries = Country::orderBy('name')->pluck('name', 'iso_code');

        return view('public.checkout.index', compact('items', 'cart', 'total', 'user', 'addresses', 'countries'));
    }

    public function store(StoreCheckoutRequest $request, PlaceOrderService $placeOrderService): RedirectResponse
    {
        $user = Auth::user();
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index');
        }

        // THE FIX: Use validated data from the Form Request
        $order = $placeOrderService->handle($user, $cart, $request->validated());

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('my-account.orders');
        }

        return view('public.checkout.success', compact('order'));
    }
}
