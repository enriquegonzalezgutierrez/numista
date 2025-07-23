<?php
// database/factories/AttributeFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Tenant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Numista\Collection\Domain\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => ucfirst(fake()->word()),
            'type' => fake()->randomElement(['text', 'number', 'date', 'select']),
            'is_filterable' => fake()->boolean(),
        ];
    }
}