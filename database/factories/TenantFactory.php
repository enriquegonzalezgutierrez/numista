<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Tenant;

class TenantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    public function definition(): array
    {
        // THE FIX: Use a closure for slug to ensure it's generated from the final 'name' attribute.
        return [
            'name' => fake()->company(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
        ];
    }
}
