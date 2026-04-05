<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
use App\Models\ProductReview; // ACTIVATE LUNAR MODEL
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Product;

class ProductReviewController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user:id,first_name,last_name')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function store(StoreProductReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'No user found with that email address.'], 404);
        }

        $product = Product::find($validated['product_id']);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Check if user bought ANY variant of this Lunar Product
        $canReview = $user->orders()
            ->where('status', 'delivered')
            ->whereHas('products.variant.product', function ($query) use ($product) {
                $query->where('lunar_products.id', $product->id);
            })
            ->exists();

        if (! $canReview) {
            return response()->json(['message' => 'You can only review products from a delivered order.'], 403);
        }

        $hasAlreadyReviewed = $product->reviews()->where('user_id', $user->id)->exists();

        if ($hasAlreadyReviewed) {
            return response()->json(['message' => 'You have already submitted a review for this product.'], 409);
        }

        $review = $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_approved' => true,
        ]);

        return response()->json([
            'message' => 'Thank you for your review! It is pending approval.',
            'review' => $review->load('user:id,first_name,last_name'),
        ], 201);
    }

    public function show(ProductReview $review): JsonResponse
    {
        $review->load('user:id,first_name,last_name');

        return response()->json($review);
    }

    public function destroy(ProductReview $review): JsonResponse
    {
        $review->delete();

        return response()->json(null, 204);
    }
}
