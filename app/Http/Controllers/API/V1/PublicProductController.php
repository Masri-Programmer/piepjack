<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicProductListResource;
use App\Http\Resources\PublicProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PublicProductController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Product::filter(request()->only(['category_id', 'search']), ['name'])
            ->when(request('category_id'), fn($q, $categoryId) => $q->category($categoryId))
            ->where('active', true)
            ->with(['category', 'items' => fn($q) => $q->select('product_id', DB::raw('MIN(price) as min_price'))->groupBy('product_id')])
            ->sort([request('sort_field', 'created_at') => request('sort_direction', 'desc')])
            ->paginateResults(request('per_page', 10));

        return PublicProductListResource::collection($query)->response();
    }

    public function show(Product $product): JsonResponse
    {
        return (new PublicProductResource(
            $product->load([
                'category',
                'items' => function ($query) {
                    $query->where('active', true)->with('options');
                },
            ])
        ))->response();
    }
}
