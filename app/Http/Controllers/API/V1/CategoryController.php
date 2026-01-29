<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Category;
use App\Models\Variation;
use App\Http\Requests\CategoryRequest;
use App\Models\VariationOption;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryListResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories.
     */
    public function all(): JsonResponse
    {
        return CategoryListResource::collection($this->categoryService->getAllCategories())->response();
    }

    /**
     * List categories with filters, sorting, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['active', 'search']);
        $searchKeys = ['name'];
        $showChildren = $request->boolean('show_children', true);
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort_field', 'created_at'); // Default to 'created_at'
        $sortDirection = $request->input('sort_direction', 'desc'); // Default to 'desc'

        // Ensure $sortField is always a string
        if (!is_string($sortField) || empty($sortField)) {
            $sortField = 'created_at';
        }

        $categories = $this->categoryService->getFilteredCategories(
            $filters,
            $searchKeys,
            $showChildren,
            $sortField,
            $sortDirection,
            $perPage
        );

        return CategoryListResource::collection($categories)->response();
    }

    /**
     * Show a single category with its children.
     */
    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getCategoryWithChildren($category->id);

        return (new CategoryResource($category))->response();
    }

    /**
     * Store a new category and its default variations.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategoryWithDefaultVariations($request->validated());

        return (new CategoryResource($category))->response();
    }

    /**
     * Update an existing category.
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $this->categoryService->updateCategory($category, $request->validated());

        return (new CategoryResource($category))->response();
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);

        return response('', 204);
    }
}
