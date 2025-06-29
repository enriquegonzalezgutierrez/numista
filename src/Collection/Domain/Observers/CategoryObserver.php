<?php

// src/Collection/Domain/Observers/CategoryObserver.php

namespace Numista\Collection\Domain\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     */
    public function creating(Category $category): void
    {
        if (empty($category->slug)) {
            $category->slug = $this->createUniqueSlug($category->name);
        }
    }

    /**
     * Handle the Category "updating" event.
     */
    public function updating(Category $category): void
    {
        if ($category->isDirty('name')) {
            $category->slug = $this->createUniqueSlug($category->name, $category->id);
        }
    }

    public function saved(Category $category): void
    {
        $this->clearTenantWidgetsCache($category->tenant_id);
    }

    public function deleted(Category $category): void
    {
        $this->clearTenantWidgetsCache($category->tenant_id);
    }

    protected function clearTenantWidgetsCache(int $tenantId): void
    {
        Cache::forget("widgets:stats_overview:tenant_{$tenantId}");
    }

    /**
     * Creates a unique slug for the category.
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
            // Reset the query for the next loop iteration
            $query = Category::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }

        return $slug;
    }
}
