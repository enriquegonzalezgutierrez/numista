<?php

// src/Collection/Domain/Models/Image.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    // THE FIX: Add 'is_featured' to the fillable array.
    protected $fillable = ['imageable_id', 'imageable_type', 'path', 'alt_text', 'order_column', 'is_featured'];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /**
     * Get the parent imageable model (Item, User, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ImageFactory
    {
        return ImageFactory::new();
    }

    /**
     * Get the full URL to the image.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => route('images.show', ['image' => $this->id]),
        );
    }
}
