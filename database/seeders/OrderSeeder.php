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
    public function run(): void
    {
        $customer1 = User::where('email', 'cliente@numista.es')->first();
        $customer2 = User::where('email', 'cliente2@numista.es')->first();
        $tenant1 = Tenant::where('slug', 'coleccion-numista')->first();
        $tenant2 = Tenant::where('slug', 'antiguedades-clasicas')->first();

        if (! $customer1 || ! $customer2 || ! $tenant1 || ! $tenant2) {
            $this->command->error('Default users or tenants not found. Cannot create orders.');

            return;
        }

        $this->command->info('Creating sample orders with tenant isolation...');

        $itemsT1 = Item::where('tenant_id', $tenant1->id)->where('status', 'for_sale')->get();
        if ($itemsT1->count() >= 2) {
            $this->createOrderForCustomer($customer1, $itemsT1->random(2));
        }

        $itemsT2 = Item::where('tenant_id', $tenant2->id)->where('status', 'for_sale')->get();
        if ($itemsT2->count() >= 2) {
            $this->createOrderForCustomer($customer2, $itemsT2->random(2));
        }
    }

    private function createOrderForCustomer(User $customer, \Illuminate\Support\Collection $items): void
    {
        if ($items->isEmpty()) {
            return;
        }
        $tenant = $items->first()->tenant;
        $total = $items->sum('sale_price');

        $order = Order::factory()->create([
            'tenant_id' => $tenant->id, 'user_id' => $customer->id, 'total_amount' => $total,
        ]);

        foreach ($items as $item) {
            $order->items()->create([
                'item_id' => $item->id, 'quantity' => 1, 'price' => $item->sale_price,
            ]);
        }
    }
}
