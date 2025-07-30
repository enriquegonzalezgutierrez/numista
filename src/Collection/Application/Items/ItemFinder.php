<?php

namespace Numista\Collection\Application\Items;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Item;

class ItemFinder
{
    public function __construct(private int $perPage = 12) {}

    public function forMarketplace(array $filters = []): Paginator
    {
        // THE FIX: Ensure that 'images' and 'tenant' are always eager-loaded.
        // This prevents the N+1 query problem on the marketplace page.
        $query = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant']);

        $this->applyFilters($query, $filters);

        return $query->latest('created_at')->simplePaginate($this->perPage)->withQueryString();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        // Search filter for item name
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
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
