<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicProductListResource;
use App\Http\Resources\PublicProductResource;
use Illuminate\Http\JsonResponse; // IMPORTANT: Using the Lunar Model
use Lunar\Models\Product;

class PublicProductController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Product::query()
            ->where('status', 'published') // Lunar uses status instead of active boolean
            ->with(['variants.prices', 'collections', 'media']); // Eager load Lunar relationships

        // 1. Filter by Category (Lunar Collections)
        if ($categoryId = request('category_id')) {
            $query->whereHas('collections', function ($q) use ($categoryId) {
                $q->where('lunar_collections.id', $categoryId);
            });
        }

        // 2. Search by Name (Querying Lunar's JSON attribute column)
        if ($search = request('search')) {
            $query->where('attribute_data->name->value', 'like', '%'.$search.'%');
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
            'variants.values.option', // This is the corrected relationship path!
            'collections',
            'media',
        ]);

        return (new PublicProductResource($product))->response();
    }
}
