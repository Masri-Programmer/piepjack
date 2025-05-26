<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Product;

class ProductController extends Controller
{
    private function getProductCounts(): array
    {
        return [
            'total_active_products' => Cache::remember('total_active_products', now()->addMinutes(10), function () {
                return Product::countByStatus(true);
            }),
            'total_inactive_products' => Cache::remember('total_inactive_products', now()->addMinutes(10), function () {
                return Product::countByStatus(false);
            }),
        ];
    }
    public function index(): JsonResponse
    {
        $query = Product::filter(request()->only(['category_id', 'active', 'search']), ['id', 'name', 'description'])
        ->withCategory()
            ->sort([
                request('sort_field', 'created_at') => request('sort_direction', 'desc'),
            ])
            ->paginateResults(request('per_page', 10));

        $productCounts = $this->getProductCounts();

        $resource = ProductListResource::collection($query);

        return $resource->additional([
            'meta' => $productCounts,
        ])->response();
    }



    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load([
            'category',
            'items' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'items.options'
        ]);

        return (new ProductResource($product))->response();
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
