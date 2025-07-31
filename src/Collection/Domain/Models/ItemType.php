<?php

// src/Collection/Domain/Models/ItemType.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\ItemTypeFactory; // THE FIX: Import the new factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * THE FIX: Explicitly link the model to its factory.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): ItemTypeFactory
    {
        return ItemTypeFactory::new();
    }
}
