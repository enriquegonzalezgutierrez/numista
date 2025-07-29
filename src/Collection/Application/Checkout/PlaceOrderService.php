<?php

namespace Numista\Collection\Application\Checkout;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;

class PlaceOrderService
{
    public function handle(User $user, array $cart, array $data): Order
    {
        $order = DB::transaction(function () use ($user, $cart, $data) {
            $address = $this->getAddress($user, $data);

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

            session()->forget('cart');

            return $order;
        });

        // Dispatch the event after the transaction has been successfully committed.
        OrderPlaced::dispatch($order);

        return $order;
    }

    private function getAddress(User $user, array $data): Address
    {
        if ($data['address_option'] === 'new') {
            return $user->customer->addresses()->create($data['shipping_address']);
        }

        return Address::find($data['selected_address_id']);
    }
}
