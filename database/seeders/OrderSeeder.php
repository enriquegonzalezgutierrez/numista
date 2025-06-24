<?php

// database/seeders/OrderSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\Tenant;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'coleccion-numista')->first();
        if (! $tenant) {
            $this->command->warn('Cannot run OrderSeeder. Missing tenant.');

            return;
        }

        // --- Create a pool of customers ---
        $customers = User::factory(5)->create(); // Create 5 random customers
        $this->command->info('Created 5 sample customers.');

        // --- Get items available for sale from this tenant ---
        $itemsForSale = Item::where('tenant_id', $tenant->id)
            ->where('status', 'for_sale')
            ->whereNotNull('sale_price')
            ->get();

        if ($itemsForSale->count() < 3) {
            $this->command->warn('Not enough items for sale to create meaningful orders. Skipping OrderSeeder.');

            return;
        }

        // --- Create 8 orders, assigning them to random customers ---
        for ($i = 0; $i < 8; $i++) {
            $customer = $customers->random();
            $orderItems = $itemsForSale->random(rand(1, 3))->unique('id'); // Ensure unique items per order

            if ($orderItems->isEmpty()) {
                continue;
            }

            // Create the order first
            $order = Order::factory()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $customer->id,
                'total_amount' => 0, // We will calculate this after adding items
            ]);

            // Create order items
            $totalAmount = 0;
            foreach ($orderItems as $item) {
                $quantity = 1; // For simplicity, each item is bought once
                $price = $item->sale_price;
                $totalAmount += $price * $quantity;

                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            // Update the order with the correct total amount
            $order->update(['total_amount' => $totalAmount]);
        }

        $this->command->info('Order seeder finished. Created 8 sample orders.');
    }
}
