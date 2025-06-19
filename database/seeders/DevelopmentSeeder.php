<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Create the Admin User ---
        // We use firstOrCreate to avoid creating duplicate users if we run the seeder multiple times.
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@numista.es'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin'),
            ]
        );

        // --- 2. Create the Tenant ---
        // Also using firstOrCreate to prevent duplicates.
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'numista-collection'],
            ['name' => 'Numista Collection']
        );

        // --- 3. Attach the User to the Tenant ---
        // We use the 'tenants' relationship (many-to-many) we defined earlier.
        // The syncWithoutDetaching() method is safe to run multiple times.
        // It ensures the user is attached to this tenant without removing other potential tenants.
        $adminUser->tenants()->syncWithoutDetaching($tenant->id);

        $this->command->info('Development seeder finished.');
        $this->command->info('Admin User: admin@numista.es');
        $this->command->info('Password: admin');
    }
}