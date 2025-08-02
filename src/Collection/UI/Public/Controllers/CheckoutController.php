<?php

// src/Collection/UI/Public/Controllers/CheckoutController.php

namespace Numista\Collection\UI\Public\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Numista\Collection\Application\Checkout\PlaceOrderService;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\UI\Public\Requests\StoreCheckoutRequest;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(): View|RedirectResponse
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index')->with('error', __('public.cart_is_empty'));
        }

        $items = Item::with('images')->whereIn('id', array_keys($cart))->get();
        $total = $items->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);

        Stripe::setApiKey(config('stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => round($total * 100),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        $user = Auth::user();
        $addresses = $user->customer->addresses;
        $countries = Country::orderBy('name')->pluck('name', 'iso_code');

        return view('public.checkout.index', [
            'items' => $items,
            'cart' => $cart,
            'total' => $total,
            'user' => $user,
            'addresses' => $addresses,
            'countries' => $countries,
            'stripeKey' => config('stripe.key'),
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function store(StoreCheckoutRequest $request, PlaceOrderService $placeOrderService): RedirectResponse
    {
        $user = Auth::user();
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('public.items.index');
        }

        $orders = $placeOrderService->handle($user, $cart, $request->validated());

        return redirect()->route('checkout.success', ['orders' => $orders->pluck('id')->implode(',')]);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $orderIds = explode(',', $request->query('orders'));

        $safeOrderIds = array_filter($orderIds, 'is_numeric');

        if (empty($safeOrderIds)) {
            return redirect()->route('my-account.orders');
        }

        $orders = Order::whereIn('id', $safeOrderIds)->where('user_id', Auth::id())->get();

        if ($orders->isEmpty()) {
            return redirect()->route('my-account.orders');
        }

        return view('public.checkout.success', compact('orders'));
    }
}
