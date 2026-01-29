<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductConfigurationResource;
use App\Models\ProductConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductConfigurationController extends Controller
{
    public function index(): JsonResponse
    {
        return ProductConfigurationResource::collection(ProductConfiguration::with('productItem', 'variationOption')->get())->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_item_id' => 'required|exists:product_items,id',
            'variation_option_id' => 'required|exists:variation_options,id',
        ]);

        $configuration = ProductConfiguration::create($validated);

        return response()->json($configuration, 201);
    }

    public function show(ProductConfiguration $productConfiguration): JsonResponse
    {
        return (new ProductConfigurationResource($productConfiguration->load('productItem', 'variationOption')))->response();
    }

    public function update(Request $request, ProductConfiguration $productConfiguration): JsonResponse
    {
        $validated = $request->validate([
            'product_item_id' => 'required|exists:product_items,id',
            'variation_option_id' => 'required|exists:variation_options,id',
        ]);

        $productConfiguration->update($validated);

        return response()->json($productConfiguration);
    }

    public function destroy(ProductConfiguration $productConfiguration): JsonResponse
    {
        $productConfiguration->delete();

        return response()->json(null, 204);
    }
}
