<?php

// src/Collection/Domain/Observers/CollectionObserver.php

namespace Numista\Collection\Domain\Observers;

use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Collection;

class CollectionObserver
{
    /**
     * Handle the Collection "creating" event.
     *
     * @param  \Numista\Collection\Domain\Models\Collection  $$collection
     * @return void
     */
    public function creating(Collection $collection): void
    {
        if (empty($collection->slug)) {
            $collection->slug = $this->createUniqueSlug($collection->name);
        }
    }

    /**
     * Handle the Collection "updating" event.
     *
     * @param  \Numista\Collection\Domain\Models\Collection  $collection
     * @return void
     */
    public function updating(Collection $collection): void
    {
        if ($collection->isDirty('name')) {
            $collection->slug = $this->createUniqueSlug($collection->name, $collection->id);
        }
    }

    /**
     * Creates a unique slug for the collection.
     *
     * @param string $name
     * @param int|null $exceptId
     * @return string
     */
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
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            // Reset the query for the next loop iteration
            $query = Collection::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }

        return $slug;
    }
}
