<?php

// src/Collection/Domain/Models/ItemType.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\ItemTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // THE FIX: Import HasMany

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * THE FIX: Define the relationship between an ItemType and its Items.
     * An ItemType can have many Items.
     * We match the `items.type` column (string) with the `item_types.name` column (string).
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'type', 'name');
    }

    /**
     * Explicitly link the model to its factory.
     */
    protected static function newFactory(): ItemTypeFactory
    {
        return ItemTypeFactory::new();
    }
}
