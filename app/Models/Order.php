<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFilters,  SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $lastOrder = self::latest()->withTrashed()
                ->latest('id')
                ->first();
            $nextNumber = $lastOrder ? $lastOrder->id + 1 : 1;
            $order->order_number = 'ORD-' . date('Ym') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            //  ORD-202502-00001
        });
    }

    protected $fillable = [
        'customer_details_id',
        'total_price',
        'status',
        'order_notes',
    ];

    protected $rules = [
        'status' => ['in:not_requested,requested,approved,rejected'],
    ];
    public static function getStatusOptions()
    {
        return ['pending', 'paid', 'canceled', 'shipped', 'delivered'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(CustomerDetail::class, 'id');
    }

    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class, 'customer_details_id');
    }

    public function returning(): HasOne
    {
        return $this->hasOne(Returning::class, 'order_id');
    }

    // public static function getPaymentStatusOptions()
    // {
    //     return ['pending', 'completed', 'failed'];
    // }

    // public static function getShippingStatusOptions()
    // {
    //     return ['not_shipped', 'shipped', 'delivered'];
    // }
}
