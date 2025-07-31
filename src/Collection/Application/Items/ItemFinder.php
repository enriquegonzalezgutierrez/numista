<?php

// src/Collection/Application/Items/ItemFinder.php

namespace Numista\Collection\Application\Items;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Item;

class ItemFinder
{
    public function __construct(private int $perPage = 12) {}

    public function forMarketplace(array $filters = []): Paginator
    {
        $query = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant']);

        $this->applyFilters($query, $filters);

        // THE FIX: Add a secondary, stable sort key (`id`) to ensure a deterministic order
        // across all database engines (local PostgreSQL vs. GitHub's SQLite).
        return $query->latest('created_at')->orderBy('id', 'desc')->simplePaginate($this->perPage)->withQueryString();
    }

    public function applyFilters(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            // If using PostgreSQL, use the powerful full-text search.
            if (DB::getDriverName() === 'pgsql') {
                // Sanitize the search query to be used with to_tsquery
                $searchQuery = implode(' & ', explode(' ', trim($search)));

                // Use whereRaw to leverage PostgreSQL's full-text search capabilities
                $query->whereRaw(
                    "to_tsvector('spanish', name || ' ' || description) @@ to_tsquery('spanish', ?)",
                    [$searchQuery]
                );
            } else {
                // For other databases (like SQLite in tests), fall back to a simple LIKE search.
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            }
        });

        // Category filter
        $query->when($filters['categories'] ?? null, function ($query, $categories) {
            if (! is_array($categories)) {
                return;
            }
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('category_id', $categories);
            });
        });

        // Collection filter
        $query->when($filters['collections'] ?? null, function ($query, $collections) {
            if (! is_array($collections)) {
                return;
            }
            $query->whereHas('collections', function ($q) use ($collections) {
                $q->whereIn('collection_id', $collections);
            });
        });

        // Dynamic Attribute Filter
        $query->when($filters['attributes'] ?? null, function ($query, $attributes) {
            foreach ($attributes as $attributeId => $value) {
                if (empty($value)) {
                    continue;
                }

                $attribute = Attribute::find($attributeId);
                if (! $attribute) {
                    continue;
                }

                $query->whereHas('attributes', function ($q) use ($attribute, $value) {
                    $q->where('attribute_id', $attribute->id);

                    if ($attribute->type === 'select') {
                        $q->where('attribute_value_id', $value);
                    } else {
                        $q->where('value', 'like', '%'.$value.'%');
                    }
                });
            }
        });
    }
}
