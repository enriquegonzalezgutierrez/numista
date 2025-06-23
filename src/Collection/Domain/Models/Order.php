<?php

namespace Numista\Collection\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'shipping_address',
        'payment_method',
        'payment_status'
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * This overrides Laravel's default convention to correctly locate the factory.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
