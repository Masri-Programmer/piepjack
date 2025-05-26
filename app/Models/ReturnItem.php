<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Returning;

class ReturnItem extends Model
{
    protected $fillable = ['return_id', 'product_item_id', 'quantity'];

    public function return()
    {
        return $this->belongsTo(Returning::class, 'return_id');
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

}
