<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFilters;

    protected $fillable = [
        'email',
        'active',
    ];

    public function details(): HasOne
    {
        return $this->hasOne(CustomerDetail::class, 'customer_id')->latestOfMany();
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class,
            CustomerDetail::class,
            'customer_id',
            'customer_details_id',
            'id',
            'id',
        );
    }
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
