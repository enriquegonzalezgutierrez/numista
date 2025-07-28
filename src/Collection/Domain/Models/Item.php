<?php

namespace Numista\Collection\Domain\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'type', 'quantity',
        'purchase_price', 'purchase_date', 'status', 'sale_price',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function getMainImage(): ?Image
    {
        return $this->images()->where('is_featured', true)->first() ?? $this->images()->orderBy('order_column')->first();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order_column');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'item_attribute_value')
            ->withPivot('value', 'attribute_value_id')
            ->withTimestamps();
    }

    protected static function newFactory(): ItemFactory
    {
        return ItemFactory::new();
    }
}
