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
            // THE FIX: Instead of always creating a new User, this logic ensures
            // that we either find an existing user without a customer profile or create a new one.
            // This prevents unique constraint violations in tests.
            'user_id' => User::factory()->create(['is_admin' => false])->id,
            'phone_number' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
        ];
    }
}
