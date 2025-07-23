<?php

// src/Collection/Domain/Models/Item.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\ItemFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These are the core, generic fields that all items share.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'type',
        'quantity',
        'purchase_price',
        'purchase_date',
        'status',
        'sale_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
    ];

    /**
     * Get the tenant that owns the item.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all of the item's images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * The categories that belong to the item.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * The collections that belong to the item.
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class);
    }

    /**
     * The order line items associated with this item.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * The attributes associated with the item.
     * This is the new, flexible relationship for EAV.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'item_attribute_value')
            ->withPivot('value', 'attribute_value_id') // Makes the 'value' from the pivot table accessible
            ->withTimestamps();
    }

    /**
     * Create a new factory instance for the model.
     *
     * This overrides Laravel's default convention to correctly locate the factory.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory(): ItemFactory
    {
        return ItemFactory::new();
    }

    /**
     * Scope a query to apply filters from the request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // Search filter for item name
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });

        // Category filter
        $query->when($filters['categories'] ?? null, function ($query, $categories) {
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        });

        return $query;
    }
}
