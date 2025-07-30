<?php

// src/Collection/Domain/Models/Customer.php

namespace Numista\Collection\Domain\Models;

use App\Models\User;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough; // <-- Importante añadir este `use`

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'shipping_address',
    ];

    /**
     * The user account associated with this customer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The orders placed by this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    /**
     * The addresses associated with this customer.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the tenants this customer has ordered from.
     * This relationship is crucial for Filament's multi-tenancy to work correctly.
     * A Customer has many Tenants through its Orders.
     */
    public function tenants(): HasManyThrough
    {
        return $this->hasManyThrough(
            Tenant::class,      // The final model we want to access (Tenant)
            Order::class,       // The intermediate model (Order)
            'user_id',          // Foreign key on the intermediate model (orders table)
            'id',               // Foreign key on the final model (tenants table)
            'user_id',          // Local key on the starting model (customers table)
            'tenant_id'         // Local key on the intermediate model (orders table)
        );
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }
}
