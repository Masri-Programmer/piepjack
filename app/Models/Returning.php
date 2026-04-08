<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Models\Order;

class Returning extends Model
{
    use HasFilters;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            $lastReturn = self::orderByDesc('id')->first();
            $nextNumber = $lastReturn ? $lastReturn->id + 1 : 1;
            $return->return_number = 'RET-'.date('Ym').'-'.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    protected $table = 'returnings';

    protected $fillable = [
        'order_id',
        'return_number',
        'status',
        'reason',
        'return_fee',
        'sendcloud_return_id',
        'label_url',
        'qr_code_url',
    ];

    public static function getStatusOptions()
    {
        return ['not_requested', 'requested', 'approved', 'rejected'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
