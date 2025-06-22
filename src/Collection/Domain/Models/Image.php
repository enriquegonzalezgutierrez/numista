<?php

// src/Collection/Domain/Models/Image.php

namespace Numista\Collection\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['imageable_id', 'imageable_type', 'path', 'alt_text', 'order_column'];

    /**
     * Get the parent imageable model (Item, User, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
