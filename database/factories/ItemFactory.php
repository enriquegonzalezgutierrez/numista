<?php

namespace Database\factories;

use App\Models\Country;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default state for any item
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(1, 5),
            'purchase_price' => fake()->randomFloat(2, 5, 100),
            'purchase_date' => fake()->date(),
            'status' => fake()->randomElement(['in_collection', 'for_sale']),
            'grade' => fake()->randomElement(['MS-60', 'VF', 'F', 'G']),
        ];
    }

    /**
     * State for a 'coin' type item.
     */
    public function coin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'coin',
            'name' => 'Coin: ' . fake()->words(2, true),
            'country_id' => Country::inRandomOrder()->first()->id,
            'year' => fake()->numberBetween(1800, 2023),
            'denomination' => fake()->randomElement(['1 Dollar', '25 Cents', '10 Pesos']),
            'mint_mark' => fake()->randomElement(['S', 'D', 'P', 'O']),
            'composition' => fake()->randomElement(['90% Silver', 'Copper-Nickel', 'Bronze']),
            'weight' => fake()->randomFloat(4, 2.5, 31.1035),
        ]);
    }

    /**
     * State for a 'banknote' type item.
     */
    public function banknote(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'banknote',
            'name' => 'Banknote: ' . fake()->words(2, true),
            'country_id' => Country::inRandomOrder()->first()->id,
            'year' => fake()->numberBetween(1900, 2020),
            'denomination' => fake()->randomElement(['100 Pesetas', '5 Dollars', '20 Euros']),
            'serial_number' => fake()->bothify('??########?'),
        ]);
    }

    /**
     * State for a 'comic' type item.
     */
    public function comic(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'comic',
            'name' => fake()->randomElement(['The Amazing Spider-Man', 'Action Comics', 'X-Men']),
            'grade' => fake()->randomElement(['CGC 9.8', 'NM', 'VF/NM']),
            'publisher' => fake()->randomElement(['Marvel', 'DC Comics', 'Image']),
            'issue_number' => fake()->numberBetween(1, 500),
            'cover_date' => fake()->date(),
        ]);
    }
}