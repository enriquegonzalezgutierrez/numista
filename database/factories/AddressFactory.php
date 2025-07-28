<?php

// database/factories/AddressFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Customer;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Parents']),
            'recipient_name' => fake()->name(),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country_code' => fake()->countryCode(),
            'phone' => fake()->phoneNumber(),
            'is_default' => false,
        ];
    }
}
