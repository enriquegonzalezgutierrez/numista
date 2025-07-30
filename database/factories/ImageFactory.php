<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Numista\Collection\Domain\Models\Image;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            // By default, it will expect an 'imageable' to be passed.
            'path' => 'placeholders/object.png',
            'alt_text' => fake()->sentence,
            'order_column' => 0,
        ];
    }
}
