<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Variation;
use App\Models\VariationOption;

class CategoryService
{
    /**
     * Get all categories.
     */
    public function getAllCategories()
    {
        return Category::all();
    }

    /**
     * Get filtered categories with pagination.
     */
    public function getFilteredCategories(array $filters, array $searchKeys, bool $showChildren, ?string $sortField = 'created_at', ?string $sortDirection = 'desc', int $perPage = 10)
    {
        // Set defaults if null
        $sortField = $sortField ?? 'created_at';
        $sortDirection = $sortDirection ?? 'desc';

        return Category::filter($filters, $searchKeys)
            ->when(!$showChildren, fn($q) => $q->whereNull('parent_id'))
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Get a category with its children.
     */
    public function getCategoryWithChildren(int $categoryId)
    {
        return Category::with('children')->find($categoryId);
    }

    /**
     * Create a new category with default variations.
     */
    public function createCategoryWithDefaultVariations(array $data)
    {
        $category = Category::create($data);

        // Create default variations
        $colorVariation = Variation::create([
            'category_id' => $category->id,
            'name' => 'color',
        ]);

        $sizeVariation = Variation::create([
            'category_id' => $category->id,
            'name' => 'size',
        ]);

        // Create default variation options
        $sizeOptions = [
            ['variation_id' => $sizeVariation->id, 'value' => 'xs'],
            ['variation_id' => $sizeVariation->id, 'value' => 's'],
            ['variation_id' => $sizeVariation->id, 'value' => 'm'],
            ['variation_id' => $sizeVariation->id, 'value' => 'l'],
            ['variation_id' => $sizeVariation->id, 'value' => 'xl'],
        ];

        $colorOptions = [
            ['variation_id' => $colorVariation->id, 'value' => 'blue'],
            ['variation_id' => $colorVariation->id, 'value' => 'white'],
            ['variation_id' => $colorVariation->id, 'value' => 'black'],
            ['variation_id' => $colorVariation->id, 'value' => 'red'],
            ['variation_id' => $colorVariation->id, 'value' => 'green'],
        ];

        VariationOption::insert($sizeOptions);
        VariationOption::insert($colorOptions);

        return $category;
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Category $category, array $data)
    {
        $category->update($data);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(Category $category)
    {
        $category->delete();
    }
}
