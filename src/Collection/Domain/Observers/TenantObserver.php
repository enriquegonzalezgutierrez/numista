<?php

namespace Numista\Collection\Domain\Observers;

use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Tenant;

class TenantObserver
{
    public function creating(Tenant $tenant): void
    {
        if (empty($tenant->slug)) {
            $tenant->slug = $this->createUniqueSlug($tenant->name);
        }
    }

    public function updating(Tenant $tenant): void
    {
        if ($tenant->isDirty('name')) {
            $tenant->slug = $this->createUniqueSlug($tenant->name, $tenant->id);
        }
    }

    // Reutilizamos la misma lógica para crear slugs únicos
    private function createUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        $query = Tenant::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = Tenant::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }
        return $slug;
    }
}
