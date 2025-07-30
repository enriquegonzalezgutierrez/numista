<?php

// database/seeders/DevelopmentSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Tenant;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up all user and tenant related data at the beginning.
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        DB::table('addresses')->truncate();
        DB::table('customers')->truncate();
        DB::table('tenant_user')->truncate();
        User::truncate();
        Tenant::truncate();
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
        $this->command->info('Cleaned previous tenants, users, and customers.');

        // --- 1. Create Tenants ---
        $this->command->info('Creating tenants...');
        $tenant1 = Tenant::factory()->create(['name' => 'Colección Numista']);
        $tenant2 = Tenant::factory()->create(['name' => 'Antigüedades Clásicas']);

        // --- 2. Create Admin Users ---
        $this->command->info('Creating admin users...');
        $admin1 = User::factory()->admin()->create(['name' => 'Admin Numista', 'email' => 'admin@numista.es', 'password' => 'admin']);
        $admin1->tenants()->sync([$tenant1->id]);

        $admin2 = User::factory()->admin()->create(['name' => 'Admin Clásicas', 'email' => 'admin2@numista.es', 'password' => 'admin2']);
        $admin2->tenants()->sync([$tenant2->id]);

        // --- 3. Create Customer Users, their Customer profiles, and then their Addresses ---
        $this->command->info('Creating customer users and their addresses...');

        $customerUser1 = User::factory()->customer()->create([
            'name' => 'Cliente de Prueba 1', 'email' => 'cliente@numista.es', 'password' => 'cliente',
        ]);
        // THE FIX: Explicitly create the Customer profile for the User.
        $customerProfile1 = Customer::factory()->create(['user_id' => $customerUser1->id]);
        // Now we can safely use the profile's ID.
        Address::factory()->count(2)->create(['customer_id' => $customerProfile1->id]);

        $customerUser2 = User::factory()->customer()->create([
            'name' => 'Cliente de Prueba 2', 'email' => 'cliente2@numista.es', 'password' => 'cliente2',
        ]);
        $customerProfile2 = Customer::factory()->create(['user_id' => $customerUser2->id]);
        Address::factory()->count(1)->create(['customer_id' => $customerProfile2->id, 'is_default' => true]);
    }
}
