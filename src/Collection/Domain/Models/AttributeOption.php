<?php

// src/Collection/Domain/Models/AttributeOption.php

namespace Numista\Collection\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'shared_attribute_id',
        'value',
    ];

    /**
     * Get the attribute that this option belongs to.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(SharedAttribute::class, 'shared_attribute_id');
    }
}
