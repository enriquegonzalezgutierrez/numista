<?php

// src/Collection/Domain/Models/SharedAttribute.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\SharedAttributeFactory; // THE FIX: Import the new factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharedAttribute extends Model
{
    use HasFactory;

    protected $table = 'shared_attributes';

    protected $fillable = [
        'name',
        'type',
        'is_filterable',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function itemTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            \Numista\Collection\Domain\Models\ItemType::class,
            'shared_attribute_item_type',
            'shared_attribute_id',
            'item_type_id'
        );
    }

    /**
     * THE FIX: Explicitly link the model to its factory.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): SharedAttributeFactory
    {
        return SharedAttributeFactory::new();
    }
}
