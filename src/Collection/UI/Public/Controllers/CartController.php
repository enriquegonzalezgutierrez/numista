<?php

// src/Collection/UI/Public/Controllers/CartController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
     * Add an item to the cart (synchronous request).
     */
    public function add(Request $request, Item $item): RedirectResponse
    {
        // Use a shared private method to handle the logic, then handle the response.
        $result = $this->addToCartLogic($item);

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    /**
     * Add an item to the cart asynchronously.
     */
    public function addAsync(Request $request, Item $item): JsonResponse
    {
        // Use the same shared logic.
        $result = $this->addToCartLogic($item);

        $statusCode = $result['success'] ? 200 : 409; // 409 Conflict for stock issues

        // Add the current cart count to the JSON response.
        $result['cartCount'] = count(session()->get('cart', []));

        return response()->json($result, $statusCode);
    }

    /**
     * Shared logic to add an item to the cart, with stock validation.
     *
     * @return array{success: bool, message: string}
     */
    private function addToCartLogic(Item $item): array
    {
        if ($item->status !== 'for_sale' || $item->quantity <= 0) {
            return ['success' => false, 'message' => __('public.checkout_error_item_not_available', ['itemName' => $item->name])];
        }

        $cart = session()->get('cart', []);
        $quantityInCart = $cart[$item->id]['quantity'] ?? 0;

        // Check if adding one more exceeds the available stock.
        if ($item->quantity < $quantityInCart + 1) {
            return ['success' => false, 'message' => __('public.cart_add_error_no_stock', ['itemName' => $item->name])];
        }

        // If item is already in cart, increment quantity.
        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity']++;
        } else {
            // Otherwise, add new item to cart.
            $cart[$item->id] = [
                'name' => $item->name,
                'quantity' => 1,
                'price' => $item->sale_price,
            ];
        }

        session()->put('cart', $cart);

        return ['success' => true, 'message' => __('public.cart_add_success')];
    }

    /**
     * Update an item's quantity in the cart.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $cart = session()->get('cart');

        if (isset($cart[$item->id]) && $request->quantity) {
            // We should also validate stock here for security.
            if ($item->quantity < $request->quantity) {
                return redirect()->route('cart.index')->with('error', __('public.cart_add_error_no_stock', ['itemName' => $item->name]));
            }
            $cart[$item->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', __('public.cart_update_success'));
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Item $item): RedirectResponse
    {
        $cart = session()->get('cart');

        if (isset($cart[$item->id])) {
            unset($cart[$item->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', __('public.cart_remove_success'));
    }
}
