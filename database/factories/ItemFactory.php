<?php

// database/factories/ItemFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => ucfirst(fake()->words(3, true)),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'description' => fake()->paragraph(2),
            'type' => 'object',
            'quantity' => 1,
            'purchase_price' => fake()->randomFloat(2, 5, 100),
            'purchase_date' => fake()->date(),
            'status' => fake()->randomElement(['in_collection', 'for_sale', 'sold']),
        ];
    }

    // THE FIX: The `coin` state now only returns attributes that will be processed
    // by the seeder. It does not attempt to set columns that don't exist on the `items` table.
    public function coin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'coin',
            'name' => 'Coin: '.ucfirst(fake()->words(2, true)),
            // These keys are not columns in the `items` table.
            // They are temporary attributes that the ItemSeeder will use.
            'year' => fake()->numberBetween(1800, 2023),
            'country' => fake()->country(),
            'denomination' => fake()->randomElement(['1 Dollar', '50 Pesetas', '100 Pesos', '2 Euros']),
            'mint_mark' => fake()->randomElement(['S', 'D', 'P', 'O', 'M']),
            'composition' => fake()->randomElement(['90% Silver', 'Copper-Nickel', 'Bronze', 'Gold']),
            'weight' => fake()->randomFloat(4, 2.5, 31.1035),
            'grade' => fake()->randomElement(['unc', 'au', 'xf', 'vf', 'f', 'g']),
        ]);
    }

    public function banknote(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'banknote',
            'name' => 'Banknote: '.ucfirst(fake()->words(2, true)),
            'year' => fake()->numberBetween(1900, 2020),
            'country' => fake()->country(),
            'denomination' => fake()->randomElement(['100 Pesetas', '5 Dollars', '20 Euros']),
            'serial_number' => fake()->bothify('??########?'),
            'grade' => fake()->randomElement(['unc', 'au', 'xf', 'vf']),
        ]);
    }

    public function stamp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'stamp',
            'name' => 'Stamp: '.fake()->country().' '.fake()->year(),
            'year' => fake()->numberBetween(1840, 2020),
            'country' => fake()->country(),
            'face_value' => fake()->randomElement(['5c', '10p', '1.00€']),
        ]);
    }

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

    public function watch(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'watch',
            'name' => 'Watch: '.fake()->company(),
            'year' => fake()->numberBetween(1950, 2024),
            'brand' => fake()->randomElement(['Rolex', 'Omega', 'Seiko', 'Casio']),
            'model' => fake()->word().' '.fake()->randomNumber(4),
            'material' => fake()->randomElement(['Stainless Steel', 'Gold', 'Titanium']),
        ]);
    }

    public function book(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'book',
            'name' => fake()->catchPhrase(),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'year' => fake()->numberBetween(1500, 2024),
            'isbn' => fake()->isbn13(),
        ]);
    }

    public function art(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'art',
            'name' => 'Artwork: '.fake()->words(3, true),
            'artist' => fake()->name(),
            'year' => fake()->numberBetween(1600, 2020),
            'dimensions' => fake()->numberBetween(20, 150).'x'.fake()->numberBetween(20, 150).' cm',
            'material' => fake()->randomElement(['Oil on canvas', 'Watercolor', 'Bronze sculpture']),
        ]);
    }

    /**
     * THE FIX: Add a 'searchable' state.
     * This uses a 'afterCreating' callback to ensure the model is synced with Meilisearch
     * right after it's persisted to the database.
     */
    public function searchable(): static
    {
        return $this->afterCreating(function (Item $item) {
            $item->searchable();
        });
    }
}
