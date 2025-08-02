<?php

// database/factories/SharedAttributeFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\SharedAttribute;

class SharedAttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SharedAttribute::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'type' => fake()->randomElement(['text', 'number', 'date', 'select']),
            'is_filterable' => fake()->boolean(),
        ];
    }
}
