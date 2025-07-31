<?php

// src/Collection/Application/Items/ItemFinder.php

namespace Numista\Collection\Application\Items;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;

class ItemFinder
{
    public function __construct(private int $perPage = 12) {}

    /**
     * Finds and paginates items for the main marketplace view, applying filters.
     */
    public function forMarketplace(array $filters = []): Paginator
    {
        $query = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant']);

        $this->applyFilters($query, $filters);

        return $query->latest('created_at')->orderBy('id', 'desc')->simplePaginate($this->perPage)->withQueryString();
    }

    /**
     * Finds and paginates items for a specific tenant's profile page.
     * This method now accepts and applies filters.
     */
    public function forTenantProfile(Tenant $tenant, array $filters = []): Paginator
    {
        // THE FIX: Start with the relationship, but then get its underlying query builder.
        // The `getQuery()` method on a relationship returns a Builder instance, satisfying the type hint.
        $query = $tenant->items()->getQuery()
            ->where('status', 'for_sale')
            ->with('images'); // Eager load images for performance

        // Now we are passing a valid Builder object to applyFilters.
        $this->applyFilters($query, $filters);

        return $query->latest()->simplePaginate($this->perPage)->withQueryString();
    }

    /**
     * Applies a set of filters to the item query builder.
     */
    public function applyFilters(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            if (DB::getDriverName() === 'pgsql') {
                $searchQuery = implode(' & ', explode(' ', trim($search)));
                $query->whereRaw(
                    "to_tsvector('spanish', name || ' ' || description) @@ to_tsquery('spanish', ?)",
                    [$searchQuery]
                );
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            }
        });

        $query->when($filters['categories'] ?? null, function ($query, $categories) {
            if (! is_array($categories)) {
                return;
            }
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('category_id', $categories);
            });
        });

        $query->when($filters['collections'] ?? null, function ($query, $collections) {
            if (! is_array($collections)) {
                return;
            }
            $query->whereHas('collections', function ($q) use ($collections) {
                $q->whereIn('collection_id', $collections);
            });
        });

        $query->when($filters['attributes'] ?? null, function ($query, $attributes) {
            foreach ($attributes as $attributeId => $value) {
                if (empty($value)) {
                    continue;
                }

                $attribute = SharedAttribute::find($attributeId);
                if (! $attribute) {
                    continue;
                }

                $query->whereHas('attributes', function ($q) use ($attribute, $value) {
                    $q->where('shared_attribute_id', $attribute->id);

                    if ($attribute->type === 'select') {
                        $q->where('attribute_option_id', $value);
                    } else {
                        $q->where('value', $value);
                    }
                });
            }
        });
    }
}
