<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function scopeSearch(Builder $query, $search, $searchKeys)
    {
        foreach ($searchKeys as $key) {
            $query->orWhere($key, 'like', "%{$search}%");
        }

        return $query;
    }

    public function scopeCategory(Builder $query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeActive(Builder $query, $status)
    {
        return $query->where('active', $status);
    }

    public function scopeSort(Builder $query, $sortFields)
    {
        foreach ($sortFields as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }


    public function scopePaginateResults(Builder $query, $perPage)
    {
        return $query->paginate($perPage)->withQueryString();
    }

    public function scopeCountByStatus(Builder $query, $status)
    {
        return $query->where('active', $status)->count();
    }

    public function scopeWithCategory(Builder $query)
    {
        return $query->with('category');
    }

    public function scopeFilter(Builder $query, $filters, $searchKeys)
    {
        return $query->when($filters['category_id'] ?? null, fn($q, $categoryId) => $q->category($categoryId))
            ->when($filters['active'] ?? null, fn($q, $active) => $q->active($active))
            ->when($filters['search'] ?? null, fn($q, $search) => $q->search($search, $searchKeys));
    }
}
