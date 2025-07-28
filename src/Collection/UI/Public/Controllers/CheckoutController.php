<?php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Numista\Collection\Application\Checkout\PlaceOrderService;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;

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

    public function store(Request $request, PlaceOrderService $placeOrderService): RedirectResponse
    {
        $user = Auth::user();
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index');
        }

        $validatedData = $request->validate([
            'address_option' => 'required|string|in:existing,new',
            'selected_address_id' => [
                'nullable',
                'required_if:address_option,existing',
                Rule::exists('addresses', 'id')->where('customer_id', $user->customer->id),
            ],
            'shipping_address.label' => 'required_if:address_option,new|string|max:255',
            'shipping_address.recipient_name' => 'required_if:address_option,new|string|max:255',
            'shipping_address.street_address' => 'required_if:address_option,new|string|max:255',
            'shipping_address.city' => 'required_if:address_option,new|string|max:255',
            'shipping_address.postal_code' => 'required_if:address_option,new|string|max:20',
            'shipping_address.country_code' => 'required_if:address_option,new|string|size:2',
            'shipping_address.state' => 'nullable|string|max:255',
            'shipping_address.phone' => 'nullable|string|max:20',
        ]);

        $order = $placeOrderService->handle($user, $cart, $validatedData);

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
