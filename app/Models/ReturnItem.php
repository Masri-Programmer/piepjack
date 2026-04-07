<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Product;

class ReturnItem extends Model
{
    protected $fillable = ['return_id', 'product_item_id', 'quantity'];

    public function return()
    {
        return $this->belongsTo(Returning::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_item_id');
    }
}
