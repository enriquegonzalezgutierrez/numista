<?php

// src/Collection/Domain/Models/Attribute.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\AttributeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'is_filterable',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Create a new factory instance for the model.
     * This overrides Laravel's default convention to correctly locate the factory.
     */
    protected static function newFactory(): AttributeFactory
    {
        return AttributeFactory::new();
    }
}
