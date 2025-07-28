<?php

namespace Numista\Collection\Domain\Models;

use App\Models\User;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }
}
