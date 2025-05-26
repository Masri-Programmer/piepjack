<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_item_id',
        'variation_option_id',
    ];

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    public function variationOption(): BelongsTo
    {
        return $this->belongsTo(VariationOption::class, 'variation_option_id');
    }
}
