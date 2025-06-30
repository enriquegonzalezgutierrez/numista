<?php

// database/seeders/DevelopmentSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Numista\Collection\Domain\Models\Tenant;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Create the Tenant using its factory ---
        $tenant = Tenant::factory()->create([
            'name' => 'Colección Numista',
        ]);

        // --- 2. Create or find the Admin User using the Model's firstOrCreate method ---
        // This is the correct way to handle "find or create" logic.
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@numista.es'],
            [
                'name' => 'Usuario Administrador',
                'password' => Hash::make('admin'), // We need to hash the password manually here
                'is_admin' => true, // Explicitly set this user as an administrator
            ]
        );

        // --- 3. Attach the User to the Tenant ---
        $adminUser->tenants()->syncWithoutDetaching($tenant->id);

        // Output info to the console
        $this->command->info('Development seeder finished.');
        $this->command->info('Admin User: admin@numista.es');
        $this->command->info('Password: admin');
    }
}
