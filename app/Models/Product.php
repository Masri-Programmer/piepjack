<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\HasFilters;

class Product extends Model
{
    use SoftDeletes, HasFactory, HasFilters;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $slug = Str::slug($model->name);
            $count = 1;

            while (Product::where('slug', $slug)->exists()) {
                $slug = Str::slug($model->name) . '-' . $count;
                $count++;
            }

            $model->slug = $slug;
        });

        static::updating(function ($model) {
            $slug = Str::slug($model->name);
            $count = 1;

            while (Product::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                $slug = Str::slug($model->name) . '-' . $count;
                $count++;
            }

            $model->slug = $slug;
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'image',
        'image_mime',
        'image_size',
        'active',
        'description',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class, 'product_id');
    }
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
}
