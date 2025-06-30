<?php

// database/seeders/OrderSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Customer;
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

        $this->command->info('Creating a large volume of customers and orders...');

        // Create a pool of 200 customers
        $customers = User::factory(200)
            ->customer()
            ->has(Customer::factory())
            ->create();
        $this->command->info('Created 200 sample customers.');

        $itemsForSale = Item::where('tenant_id', $tenant->id)
            ->where('status', 'for_sale')
            ->whereNotNull('sale_price')
            ->get();

        if ($itemsForSale->count() < 10) { // Need more items for variety
            $this->command->warn('Not enough items for sale to create meaningful orders. Skipping OrderSeeder.');

            return;
        }

        // Create 400 orders
        for ($i = 0; $i < 400; $i++) {
            $customerUser = $customers->random();
            $orderItems = $itemsForSale->random(rand(1, 4))->unique('id');

            if ($orderItems->isEmpty()) {
                continue;
            }

            $order = Order::factory()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $customerUser->id,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            foreach ($orderItems as $item) {
                $quantity = rand(1, 2);
                $price = $item->sale_price;
                $totalAmount += $price * $quantity;

                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);
        }

        $this->command->info('Order seeder finished. Created 400 sample orders.');
    }
}
