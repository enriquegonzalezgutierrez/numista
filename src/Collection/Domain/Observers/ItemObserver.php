<?php

// src/Collection/Domain/Observers/ItemObserver.php

namespace Numista\Collection\Domain\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Item;

class ItemObserver
{
    public function creating(Item $item): void
    {
        if (empty($item->slug)) {
            $item->slug = $this->createUniqueSlug($item->name);
        }
    }

    public function updating(Item $item): void
    {
        if ($item->isDirty('name')) {
            $item->slug = $this->createUniqueSlug($item->name, $item->id);
        }
    }

    public function saved(Item $item): void
    {
        $this->clearTenantWidgetsCache($item->tenant_id);
    }

    public function deleted(Item $item): void
    {
        $this->clearTenantWidgetsCache($item->tenant_id);
    }

    protected function clearTenantWidgetsCache(int $tenantId): void
    {
        Cache::forget("widgets:stats_overview:tenant_{$tenantId}");
        // Note: Add keys for other cached widgets here in the future
    }

    private function createUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        $query = Item::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        while ($query->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
            // Reset the query for the next loop iteration
            $query = Item::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }

        return $slug;
    }
}
