<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_item_id',
        'quantity',
        'price_per_item',
    ];

    public function productItem(): HasOne
    {
        return $this->hasOne(ProductItem::class, 'id', 'product_item_id');
    }

    public function returns()
    {
        return $this->hasManyThrough(ReturnItem::class, Returning::class);
    }
    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'product_item_id', 'id');
    }

}
