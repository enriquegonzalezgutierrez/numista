<?php
// database/factories/ItemFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Numista\Collection\Domain\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // We now rely on the globally configured Faker instance, which is
        // set to 'es_ES' in the DatabaseSeeder before this factory is called.
        return [
            'name' => ucfirst(fake()->words(3, true)),
            'description' => fake()->paragraph(2),
            'quantity' => fake()->numberBetween(1, 5),
            'purchase_price' => fake()->randomFloat(2, 5, 100),
            'purchase_date' => fake()->date(),
            'status' => fake()->randomElement(['in_collection', 'for_sale', 'sold', 'featured', 'discounted']),
            'grade' => fake()->randomElement(['unc', 'au', 'xf', 'vf', 'f', 'g']),
        ];
    }

    /**
     * State for a 'coin' type item.
     */
    public function coin(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'coin',
            'name' => 'Moneda: ' . ucfirst(fake()->words(2, true)),
            'country_id' => Country::inRandomOrder()->first()?->id,
            'year' => fake()->numberBetween(1800, 2023),
            'denomination' => fake()->randomElement(['1 Dólar', '50 Pesetas', '100 Pesos', '2 Euros']),
            'mint_mark' => fake()->randomElement(['S', 'D', 'P', 'O', 'M']),
            'composition' => fake()->randomElement(['90% Plata', 'Cobre-Níquel', 'Bronce', 'Oro']),
            'weight' => fake()->randomFloat(4, 2.5, 31.1035),
        ]);
    }

    /**
     * State for a 'banknote' type item.
     */
    public function banknote(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'banknote',
            'name' => 'Billete: ' . ucfirst(fake()->words(2, true)),
            'country_id' => Country::inRandomOrder()->first()?->id,
            'year' => fake()->numberBetween(1900, 2020),
            'denomination' => fake()->randomElement(['100 Pesetas', '5 Dólares', '20 Euros', '50 Reales']),
            'serial_number' => fake()->bothify('??########?'),
        ]);
    }

    /**
     * State for a 'comic' type item.
     */
    public function comic(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'comic',
            'name' => fake()->randomElement(['The Amazing Spider-Man', 'Action Comics', 'X-Men', 'Watchmen']),
            'grade' => fake()->randomElement(['CGC 9.8', 'NM', 'VF/NM', 'F/VF']),
            'publisher' => fake()->randomElement(['Marvel', 'DC Comics', 'Image', 'Vertigo']),
            'issue_number' => fake()->numberBetween(1, 500),
            'cover_date' => fake()->date(),
        ]);
    }

    /**
     * State for a 'watch' type item.
     */
    public function watch(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'watch',
            'name' => 'Reloj: ' . fake()->company(),
            'brand' => fake()->randomElement(['Rolex', 'Omega', 'Seiko', 'Casio']),
            'model' => fake()->word() . ' ' . fake()->randomNumber(4),
            'material' => fake()->randomElement(['Acero Inoxidable', 'Oro', 'Titanio']),
        ]);
    }

    /**
     * State for a 'stamp' type item.
     */
    public function stamp(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'stamp',
            'name' => 'Sello: ' . fake()->country() . ' ' . fake()->year(),
            'country_id' => Country::inRandomOrder()->first()->id,
            'year' => fake()->numberBetween(1840, 2020),
            'face_value' => fake()->randomElement(['5c', '10p', '1.00€']),
        ]);
    }

    /**
     * State for a 'book' type item.
     */
    public function book(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'book',
            'name' => fake()->catchPhrase(),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'year' => fake()->numberBetween(1500, 2024),
            'isbn' => fake()->isbn13(),
        ]);
    }
}
