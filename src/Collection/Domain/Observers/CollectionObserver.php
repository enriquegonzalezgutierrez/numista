<?php

namespace Numista\Collection\Domain\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Collection;

class CollectionObserver
{
    public function creating(Collection $collection): void
    {
        if (empty($collection->slug)) {
            $collection->slug = $this->createUniqueSlug($collection->name);
        }
    }

    public function updating(Collection $collection): void
    {
        if ($collection->isDirty('name')) {
            $collection->slug = $this->createUniqueSlug($collection->name, $collection->id);
        }
    }

    public function saved(Collection $collection): void
    {
        $this->clearTenantWidgetsCache($collection->tenant_id);
    }

    public function deleted(Collection $collection): void
    {
        $this->clearTenantWidgetsCache($collection->tenant_id);
    }

    protected function clearTenantWidgetsCache(int $tenantId): void
    {
        Cache::forget("widgets:stats_overview:tenant_{$tenantId}");
    }

    private function createUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        $query = Collection::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        while ($query->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
            $query = Collection::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }

        return $slug;
    }
}
