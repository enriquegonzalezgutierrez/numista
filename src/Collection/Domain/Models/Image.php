<?php

// src/Collection/Domain/Models/Image.php

namespace Numista\Collection\Domain\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

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
     * THE FIX: The URL accessor now points to a route that uses the Image ID.
     * This allows for proper authorization checks in the controller.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => route('images.show', ['image' => $this->id]),
        );
    }
}
