<?php

namespace Numista\Collection\Domain\Models;

use App\Models\User;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The users that belong to the tenant.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the items for the tenant.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }
}
