<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id', 'name', 'description', 'type', 'quantity', 'purchase_price',
        'purchase_date', 'status', 'sale_price', 'country_id', 'year',
        'denomination', 'grade', 'mint_mark', 'composition', 'weight',
        'serial_number', 'publisher', 'series_title', 'issue_number', 'cover_date',
    ];

    /**
     * Get the tenant that owns the item.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the country of the item.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}