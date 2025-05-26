<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'quantity',
        'image',
        'image_mime',
        'image_size',
        'active',
        'price',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(ProductConfiguration::class, 'product_item_id');
    }

    public function options(): HasManyThrough
    {
        return $this->hasManyThrough(
            VariationOption::class,
            ProductConfiguration::class,
            'product_item_id',
            'id',
            'id',
            'variation_option_id'
        )->with('variation');
    }
}
