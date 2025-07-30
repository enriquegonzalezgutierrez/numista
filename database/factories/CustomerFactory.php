<?php

// database/factories/CustomerFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\Customer;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            // THE FIX: Instead of creating a new User every time, this finds a User
            // that doesn't have a Customer profile yet, or creates a new one if none are available.
            // This prevents the unique constraint violation.
            'user_id' => User::factory()->create(['is_admin' => false])->id,
            'phone_number' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
        ];
    }
}
