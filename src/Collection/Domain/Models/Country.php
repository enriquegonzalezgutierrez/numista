<?php

namespace Numista\Collection\Domain\Models;

use Database\Factories\CountryFactory; // Import the factory
use Illuminate\Database\Eloquent\Factories\HasFactory; // Import the trait
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // THE FIX: Add the HasFactory trait
    use HasFactory;

    // It's good practice to define fillable properties, even for simple models
    protected $fillable = ['name', 'iso_code'];

    // This model doesn't need timestamps
    public $timestamps = false;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CountryFactory
    {
        return CountryFactory::new();
    }
}
