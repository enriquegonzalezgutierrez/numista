<?php

// src/Collection/Domain/Observers/CategoryObserver.php

namespace Numista\Collection\Domain\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Tenant; // THE FIX: Import Tenant model

class CategoryObserver
{
    public function creating(Category $category): void
    {
        if (empty($category->slug)) {
            $category->slug = $this->createUniqueSlug($category->name);
        }
    }

    public function updating(Category $category): void
    {
        if ($category->isDirty('name')) {
            $category->slug = $this->createUniqueSlug($category->name, $category->id);
        }
    }

    public function saved(Category $category): void
    {
        // THE FIX: Since categories are global, a change affects all tenants.
        // We must clear the cache for every tenant.
        $this->clearAllTenantWidgetsCache();
    }

    public function deleted(Category $category): void
    {
        // THE FIX: Same logic applies on deletion.
        $this->clearAllTenantWidgetsCache();
    }

    /**
     * Clear the stats overview widget cache for all tenants.
     */
    protected function clearAllTenantWidgetsCache(): void
    {
        // Get all tenant IDs
        $tenantIds = Tenant::pluck('id');

        // Loop through each ID and forget its specific cache key
        foreach ($tenantIds as $tenantId) {
            Cache::forget("widgets:stats_overview:tenant_{$tenantId}");
        }
    }

    // THE FIX: This old method is no longer used and can be removed.
    /*
    protected function clearTenantWidgetsCache(int $tenantId): void
    {
        Cache::forget("widgets:stats_overview:tenant_{$tenantId}");
    }
    */

    private function createUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        $query = Category::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        while ($query->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
            $query = Category::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }

        return $slug;
    }
}
