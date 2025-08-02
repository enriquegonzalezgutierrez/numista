<?php

// src/Collection/Domain/Models/Category.php

namespace Numista\Collection\Domain\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Category extends Model
{
    use HasFactory, HasRecursiveRelationships;

    // THE FIX: Removed 'tenant_id' from the fillable array.
    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    /*
     * THE FIX: The relationship to a Tenant is removed, as categories are now global.
     *
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
