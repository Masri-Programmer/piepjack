<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicProductListResource;
use App\Http\Resources\PublicProductResource;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Product;

class PublicProductController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Product::query()
            ->where('status', 'published') // Lunar uses status instead of active boolean
            ->with(['variants.prices', 'collections', 'media']) // Eager load Lunar relationships
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)]);

        // 1. Filter by Category (Lunar Collections)
        if ($categoryId = request('category_id')) {
            $query->whereHas('collections', function ($q) use ($categoryId) {
                $q->where('lunar_collections.id', $categoryId);
            });
        }

        // 2. Search by Name (Querying Lunar's JSON attribute column)
        if ($search = request('search')) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.name.value.en")) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.name.value.de")) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.description.value.en")) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.description.value.de")) LIKE ?', ["%{$search}%"]);
            });
        }

        // 3. Sorting and Pagination
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $products = $query->orderBy($sortField, $sortDirection)
            ->paginate(request('per_page', 8));

        return PublicProductListResource::collection($products)->response();
    }

    public function show(Product $product): JsonResponse
    {
        // Ensure we load the necessary Lunar relationships for a single product view
        $product->load([
            'variants.prices',
            'variants.values.option',
            'collections',
            'media',
        ])->loadAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->loadCount(['reviews' => fn ($q) => $q->where('is_approved', true)]);

        return (new PublicProductResource($product))->response();
    }
}
