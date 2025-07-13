<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasFilters, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'billing_address_id',
        'total_price',
        'status',
        'order_notes',
    ];

    /**
     * Setup model event hooks
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate an order number when creating a new order.
        static::creating(function ($order) {
            $lastOrder = self::latest('id')->withTrashed()->first();
            $nextNumber = $lastOrder ? $lastOrder->id + 1 : 1;
            $order->order_number = 'ORD-' . date('Ym') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get the valid status options for an order.
     */
    public static function getStatusOptions(): array
    {
        return ['pending', 'paid', 'canceled', 'shipped', 'delivered'];
    }

    /**
     * Get the user that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Get the primary address for the order (shipping).
     * This is a convenience method for controllers that need a single address.
     */
    public function address(): BelongsTo
    {
        return $this->shippingAddress();
    }

    /**
     * Get all of the products for the order.
     */
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    /**
     * Get the return associated with the order.
     */
    public function returning(): HasOne
    {
        return $this->hasOne(Returning::class, 'order_id');
    }
}
