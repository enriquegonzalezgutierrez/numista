<?php

// src/Collection/Application/Checkout/PlaceOrderService.php

namespace Numista\Collection\Application\Checkout;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;

class PlaceOrderService
{
    /**
     * Handles the entire process of placing an order.
     * This method now returns a collection of Orders, as one cart
     * can result in multiple orders if items from different tenants are present.
     *
     * @return \Illuminate\Support\Collection<int, \Numista\Collection\Domain\Models\Order>
     *
     * @throws \Exception
     */
    public function handle(User $user, array $cart, array $data): Collection
    {
        $itemIds = array_keys($cart);

        // First, validate ALL items BEFORE starting the transaction.
        // We lock them here to ensure data consistency from this point forward.
        $itemsInCart = Item::whereIn('id', $itemIds)->lockForUpdate()->get();

        // Check if any item from the cart has been deleted from the database.
        if ($itemsInCart->count() !== count($itemIds)) {
            throw new \Exception(__('public.checkout_error_items_not_found'));
        }

        // Check the status and quantity of each item.
        foreach ($itemsInCart as $item) {
            if ($item->status !== 'for_sale' || $item->quantity < $cart[$item->id]['quantity']) {
                throw new \Exception(__('public.checkout_error_item_not_available', ['itemName' => $item->name]));
            }
        }

        // If all validations pass, proceed with the transaction.
        $orders = DB::transaction(function () use ($user, $cart, $data, $itemsInCart) {
            $address = $this->getAddress($user, $data);
            $shippingAddressText = "{$address->recipient_name}\n{$address->street_address}\n{$address->postal_code} {$address->city}, {$address->country_code}";

            $itemsByTenant = $itemsInCart->groupBy('tenant_id');
            $createdOrders = new Collection;

            foreach ($itemsByTenant as $tenantId => $tenantItems) {
                $totalForTenant = $tenantItems->sum(fn ($item) => $item->sale_price * $cart[$item->id]['quantity']);

                $order = Order::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'order_number' => 'ORD-'.strtoupper(uniqid()),
                    'total_amount' => $totalForTenant,
                    'status' => 'paid',
                    'shipping_address' => $shippingAddressText,
                    'payment_method' => 'Stripe (Placeholder)',
                    'payment_status' => 'successful',
                ]);

                foreach ($tenantItems as $item) {
                    $order->items()->create([
                        'item_id' => $item->id,
                        'quantity' => $cart[$item->id]['quantity'],
                        'price' => $item->sale_price,
                    ]);
                    $item->decrement('quantity', $cart[$item->id]['quantity']);
                }

                $createdOrders->push($order);
            }

            session()->forget('cart');

            return $createdOrders;
        });

        // Dispatch an event for each order that was created after the transaction was successful.
        foreach ($orders as $order) {
            OrderPlaced::dispatch($order);
        }

        return $orders;
    }

    /**
     * Retrieve an existing address or create a new one based on user input.
     */
    private function getAddress(User $user, array $data): Address
    {
        if ($data['address_option'] === 'new') {
            return $user->customer->addresses()->create($data['shipping_address']);
        }

        return Address::find($data['selected_address_id']);
    }
}
