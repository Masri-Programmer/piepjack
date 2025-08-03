<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductItemListResource;
use App\Http\Resources\ProductItemResource;
use App\Models\ProductConfiguration;
use App\Models\VariationOption;
use App\Models\ProductItem;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductItemController extends Controller
{
    public function index(): JsonResponse
    {
        $product_id = request('product_id', null);

        $productItems = ProductItem::when($product_id, fn($q) => $q->where('product_id', $product_id))->get();

        return ProductItemListResource::collection($productItems)->response();
    }

    public function show(ProductItem $productItem): JsonResponse
    {
        return (new ProductItemResource($productItem->load('options', 'product')))->response();
    }

    public function store(Request $request): JsonResponse
    {
        // Fetch product and category
        $product = Product::findOrFail($request->input('product_id'));
        $category_id = $product->category_id;

        // Fetch valid variation IDs for this category
        $variation_ids = Variation::where('category_id', $category_id)->pluck('id');

        // Validate incoming request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'image' => 'nullable|string',
            'image_mime' => 'nullable|string',
            'image_size' => 'nullable',
            'price' => 'required|numeric',
            'active' => 'required|boolean',
            'variations' => 'array|required',
        ]);

        $variations = $validated['variations'];

        // Check if a product item with the same product_id and variations already exists
        $existingItem = ProductItem::where('product_id', $validated['product_id'])
        ->whereHas('configurations', function ($query) use ($variations) {
            $query->whereIn(
                'variation_option_id',
                $variations
            )
                ->select('product_item_id')
                ->groupBy('product_item_id')
                ->havingRaw('COUNT(DISTINCT variation_option_id) = ?', [count($variations)]);
        })
            ->first();

        if ($existingItem) {
            return response()->json(['message' => 'Product item with the same variations already exists.'], 409);
        }

        // Validate if the variation count matches the expected count
        if (count($variations) !== $variation_ids->count()) {
            return response()->json(['message' => 'Invalid number of variations.'], 422);
        }

        // Validate each variation
        foreach ($variations as $variation) {
            $variation_option = VariationOption::findOrFail($variation);

            if (! in_array($variation_option->variation_id, $variation_ids->toArray())) {
                return response()->json(['message' => 'Invalid variation option.'], 422);
            }

            // Remove from the variation_ids list
            $variation_ids = $variation_ids->reject(function ($id) use ($variation_option) {
                return $id === $variation_option->variation_id;
            });
        }

        // Transaction block
        DB::beginTransaction();

        try {
            // Create the product item
            $item = ProductItem::create($validated);

            // Create product configurations for variations
            foreach ($validated['variations'] as $variation) {
                ProductConfiguration::create([
                    'product_item_id' => $item->id,
                    'variation_option_id' => $variation,
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json($item, 201);
        } catch (Exception $e) {
            // Rollback on failure
            DB::rollback();

            return response()->json(['message' => 'Failed to store product item.', 'error' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id): JsonResponse
    {
        // Fetch the product item to update
        $item = ProductItem::findOrFail($id);
        $product = Product::findOrFail($item->product_id);
        $category_id = $product->category_id;

        // Fetch valid variation IDs for this category
        $variation_ids = Variation::where('category_id', $category_id)->pluck('id');

        // Validate the incoming request
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'image' => 'nullable|string',
            'image_mime' => 'nullable|string',
            'image_size' => 'nullable',
            'price' => 'required|numeric',
            'active' => 'required|boolean',
            'variations' => 'array|required',
        ]);

        $variations = $validated['variations'];

        // Check if a different product item with the same product_id and variations exists
        $existingItem = ProductItem::where('product_id', $item->product_id)
            ->where('id', '!=', $item->id) // Exclude the current item
            ->whereHas('configurations', function ($query) use ($variations) {
                $query->select('product_item_id')
                ->whereIn('variation_option_id', $variations)
                    ->groupBy('product_item_id')
                    ->havingRaw('COUNT(DISTINCT variation_option_id) = ?', [count($variations)]);
            })
            ->first();
        if ($existingItem) {
            return response()->json(['message' => 'Another product item with the same variations already exists.'], 409);
        }

        // Validate if the variation count matches the expected count
        if (count($variations) !== $variation_ids->count()) {
            return response()->json(['message' => 'Invalid number of variations.'], 422);
        }

        // Validate each variation
        foreach ($variations as $variation) {
            $variation_option = VariationOption::findOrFail($variation);

            if (! in_array($variation_option->variation_id, $variation_ids->toArray())) {
                return response()->json(['message' => 'Invalid variation option.'], 422);
            }

            // Remove from the variation_ids list
            $variation_ids = $variation_ids->reject(function ($id) use ($variation_option) {
                return $id === $variation_option->variation_id;
            });
        }

        // Transaction block for update
        DB::beginTransaction();

        try {
            // Update the product item
            $item->update($validated);

            // Update variations by deleting old ones and inserting new ones
            ProductConfiguration::where('product_item_id', $item->id)->delete();

            foreach ($validated['variations'] as $variation) {
                ProductConfiguration::create([
                    'product_item_id' => $item->id,
                    'variation_option_id' => $variation,
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json($item, 200);
        } catch (Exception $e) {
            // Rollback on failure
            DB::rollback();

            return response()->json(['message' => 'Failed to update product item.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(ProductItem $productItem): JsonResponse
    {
        $productItem->delete();

        return response()->json(null, 204);
    }
}
