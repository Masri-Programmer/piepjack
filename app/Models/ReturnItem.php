<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\ProductVariant;

class ReturnItem extends Model
{
    protected $fillable = ['return_id', 'product_item_id', 'quantity'];

    public function return()
    {
        return $this->belongsTo(Returning::class, 'return_id');
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_item_id');
    }

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(Returning::class, 'return_id');
    }
}
