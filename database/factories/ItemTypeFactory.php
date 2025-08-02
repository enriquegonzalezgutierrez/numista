<?php

// database/factories/ItemTypeFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\ItemType;

class ItemTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ItemType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // By default, it will generate a random word for the item type name.
            'name' => fake()->unique()->word(),
        ];
    }
}
