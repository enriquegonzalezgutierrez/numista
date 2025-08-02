<?php

// src/Collection/Application/Items/ItemFinder.php

namespace Numista\Collection\Application\Items;

use Illuminate\Contracts\Pagination\Paginator;
use Numista\Collection\Domain\Models\AttributeOption;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;

class ItemFinder
{
    public function __construct(private int $perPage = 12) {}

    public function forMarketplace(array $filters = []): Paginator
    {
        $query = Item::search($filters['search'] ?? '*', function ($meilisearch, $query, $options) use ($filters) {

            $filterClauses = ['status = for_sale'];

            // Handle category IDs from the filter, converting them to names for the search index.
            if (! empty($filters['categories'])) {
                $categoryNames = Category::whereIn('id', $filters['categories'])->pluck('name')->all();
                if (! empty($categoryNames)) {
                    $categoryFilters = collect($categoryNames)->map(fn ($cat) => "categories = '{$cat}'")->implode(' OR ');
                    $filterClauses[] = "({$categoryFilters})";
                }
            }

            // Handle attribute filters, converting them to text values for the search index.
            if (! empty($filters['attributes'])) {
                $attributeValues = $this->resolveAttributeValues($filters['attributes']);
                foreach ($attributeValues as $value) {
                    // Escape single quotes in the value to prevent errors in Meilisearch filter syntax
                    $escapedValue = str_replace("'", "\'", $value);
                    $filterClauses[] = "attributes = '{$escapedValue}'";
                }
            }

            $options['filter'] = implode(' AND ', $filterClauses);

            return $meilisearch->search($query, $options);
        });

        $query->query(fn ($builder) => $builder->with(['images', 'tenant']));

        return $query->simplePaginate($this->perPage)->withQueryString();
    }

    public function forTenantProfile(Tenant $tenant, array $filters = []): Paginator
    {
        // This method would need similar logic if you use advanced filters on the tenant page.
        $query = Item::search($filters['search'] ?? '*', function ($meilisearch, $query, $options) use ($tenant) {
            $filterClauses = ['status = for_sale', "tenant_name = '{$tenant->name}'"];
            // You can add the same category/attribute filter logic here if needed for tenant pages
            $options['filter'] = implode(' AND ', $filterClauses);

            return $meilisearch->search($query, $options);
        });
        $query->query(fn ($builder) => $builder->with('images'));

        return $query->simplePaginate($this->perPage)->withQueryString();
    }

    /**
     * Resolves an array of attribute filters into an array of text values for Meilisearch.
     * It correctly distinguishes between select option IDs and direct text/number values.
     */
    private function resolveAttributeValues(array $attributes): array
    {
        $attributeTypes = SharedAttribute::whereIn('id', array_keys($attributes))->pluck('type', 'id');
        $resolvedValues = [];
        $optionIdsToTranslate = [];

        foreach ($attributes as $attributeId => $value) {
            $type = $attributeTypes->get($attributeId);
            if ($type === 'select') {
                $optionIdsToTranslate[] = $value;
            } else {
                $resolvedValues[] = $value;
            }
        }

        if (! empty($optionIdsToTranslate)) {
            $translatedValues = AttributeOption::whereIn('id', $optionIdsToTranslate)->pluck('value')->all();
            $resolvedValues = array_merge($resolvedValues, $translatedValues);
        }

        return $resolvedValues;
    }
}
