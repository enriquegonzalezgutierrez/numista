<?php

// src/Collection/Application/Checkout/PlaceOrderService.php

namespace Numista\Collection\Application\Checkout;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;

class PlaceOrderService
{
    /**
     * Handles the entire process of placing an order.
     *
     * @param  \App\Models\User  $user  The user placing the order.
     * @param  array  $cart  The contents of the shopping cart.
     * @param  array  $data  The validated data from the checkout request.
     * @return \Numista\Collection\Domain\Models\Order The created order.
     *
     * @throws \Exception If any item in the cart is invalid or out of stock.
     */
    public function handle(User $user, array $cart, array $data): Order
    {
        $order = DB::transaction(function () use ($user, $cart, $data) {
            $address = $this->getAddress($user, $data);

            $shippingAddressText = "{$address->recipient_name}\n{$address->street_address}\n{$address->postal_code} {$address->city}, {$address->country_code}";

            $itemIds = array_keys($cart);
            // Lock the items to prevent race conditions where two users buy the last item at the same time.
            $itemsInCart = Item::whereIn('id', $itemIds)->lockForUpdate()->get()->keyBy('id');

            // Validate all items in the cart before proceeding.
            foreach ($itemIds as $itemId) {
                if (! isset($itemsInCart[$itemId])) {
                    throw new \Exception("Item with ID {$itemId} not found.");
                }

                $item = $itemsInCart[$itemId];

                if ($item->status !== 'for_sale') {
                    throw new \Exception('Item is not available for sale.');
                }

                if ($item->quantity < $cart[$itemId]['quantity']) {
                    throw new \Exception('Insufficient stock for item.');
                }
            }

            $total = $itemsInCart->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);

            $order = Order::create([
                'tenant_id' => $itemsInCart->first()->tenant_id,
                'user_id' => $user->id,
                'address_id' => $address->id,
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'total_amount' => $total,
                'status' => 'paid', // Assuming immediate payment success
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

                // Optional: Decrement stock. The 'UpdateSoldItemStatus' listener handles changing status.
                $item->decrement('quantity', $cart[$item->id]['quantity']);
            }

            session()->forget('cart');

            return $order;
        });

        // Dispatch the event after the transaction has been successfully committed.
        OrderPlaced::dispatch($order);

        return $order;
    }

    /**
     * Retrieves or creates the shipping address for the order based on user input.
     */
    private function getAddress(User $user, array $data): Address
    {
        if ($data['address_option'] === 'new') {
            return $user->customer->addresses()->create($data['shipping_address']);
        }

        return Address::find($data['selected_address_id']);
    }
}
