<?php

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
        'country_id',
        'year',
        'denomination',
        'grade',
        'mint_mark',
        'composition',
        'weight',
        'serial_number',
        'publisher',
        'series_title',
        'issue_number',
        'cover_date',
        'brand',
        'model',
        'material',
        'author',
        'isbn',
        'artist',
        'dimensions',
        'gemstone',
        'license_plate',
        'chassis_number',
        'record_label',
        'face_value',
        'publisher_postcard',
        'origin_location',
        'photographer',
        'location',
        'technique',
        'conflict',
        'sport',
        'team',
        'player',
        'event',
        'movie_title',
        'character',
        'images',
    ];

    protected $casts = [
        //
    ];

    /**
     * Get the tenant that owns the item.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the country of the item.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all of the item's images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get all of the item's categories.
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
