<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, HasFilters;

    protected $fillable = [
        'name',
        'active',
        'promoted',
        'parent_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $slug = Str::slug($model->name);
            $count = 1;

            while (Category::where('slug', $slug)->exists()) {
                $slug = Str::slug($model->name) . '-' . $count;
                $count++;
            }

            $model->slug = $slug;
        });

        static::updating(function ($model) {
            $slug = Str::slug($model->name);
            $count = 1;

            while (Category::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                $slug = Str::slug($model->name) . '-' . $count;
                $count++;
            }

            $model->slug = $slug;
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class, 'category_id');
    }
}
