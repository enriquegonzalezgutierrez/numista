<?php

// src/Collection/UI/Public/Controllers/CheckoutController.php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order; // Import the Country model

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
            return redirect()->route('public.items.index')->with('error', 'Your cart is empty.');
        }

        $items = Item::with('images')->whereIn('id', array_keys($cart))->get();
        $total = $items->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);
        $user = Auth::user();
        $addresses = $user->customer->addresses;

        // THE FIX: Fetch countries from the database and pass them to the view
        $countries = Country::orderBy('name')->pluck('name', 'iso_code');

        return view('public.checkout.index', compact('items', 'cart', 'total', 'user', 'addresses', 'countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index');
        }

        $request->validate([
            'address_option' => 'required|string',
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
        ]);

        $order = DB::transaction(function () use ($user, $cart, $request) {
            $address = null;
            $shippingAddressText = '';

            if ($request->address_option === 'new') {
                $newAddressData = $request->input('shipping_address');
                $newAddressData['is_default'] = $request->boolean('save_address');
                $address = $user->customer->addresses()->create($newAddressData);
            } else {
                $address = Address::find($request->selected_address_id);
            }

            $shippingAddressText = "{$address->recipient_name}\n{$address->street_address}\n{$address->postal_code} {$address->city}, {$address->country_code}";

            $itemsInCart = Item::whereIn('id', array_keys($cart))->get();
            $total = $itemsInCart->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);

            $order = Order::create([
                'tenant_id' => $itemsInCart->first()->tenant_id,
                'user_id' => $user->id,
                'address_id' => $address->id,
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'total_amount' => $total,
                'status' => 'paid',
                'shipping_address' => $shippingAddressText,
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

        $request->session()->forget('cart');

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
