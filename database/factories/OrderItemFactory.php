<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\OrderItem;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'item_id' => Item::factory(), // Creates a new item for each order item
            'quantity' => fake()->numberBetween(1, 3),
            'price' => fake()->randomFloat(2, 10, 100),
        ];
    }
}
