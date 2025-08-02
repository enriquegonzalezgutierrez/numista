<?php

// src/Collection/Domain/Models/Item.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Item extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'type', 'quantity',
        'purchase_price', 'purchase_date', 'status', 'sale_price',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function toSearchableArray(): array
    {
        $this->load(['tenant', 'categories', 'customAttributes']);

        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'sale_price' => $this->sale_price,
        ];

        $array['tenant_name'] = $this->tenant->name;
        $array['categories'] = $this->categories->pluck('name')->all();
        $array['attributes'] = $this->customAttributes->pluck('pivot.value')->all();

        return $array;
    }

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

    /**
     * THE FIX: The relationship to Category must be belongsToMany.
     */
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

    /**
     * The relationship to SharedAttribute, renamed to avoid conflicts.
     */
    public function customAttributes(): BelongsToMany
    {
        return $this->belongsToMany(SharedAttribute::class, 'item_attribute')
            ->withPivot('value', 'attribute_option_id')
            ->withTimestamps();
    }

    protected static function newFactory(): ItemFactory
    {
        return ItemFactory::new();
    }
}
