<?php

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
        // 1. Create the Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@numista.es'],
            [
                'name' => 'Usuario Administrador', // Spanish name
                'password' => Hash::make('admin'),
            ]
        );

        // 2. Create the Tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'coleccion-numista'], // Spanish slug
            ['name' => 'Colección Numista']   // Spanish name
        );

        // 3. Attach the User to the Tenant
        $adminUser->tenants()->syncWithoutDetaching($tenant->id);

        // Output info to the console
        $this->command->info('Development seeder finished.');
        $this->command->info('Admin User: admin@numista.es');
        $this->command->info('Password: admin');
    }
}