<?php

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
            // Associate with a User factory by default
            'user_id' => User::factory(),
            'phone_number' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
        ];
    }
}
