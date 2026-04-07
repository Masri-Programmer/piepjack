<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Order;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class ProductReviewController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $reviews = ProductReview::where('product_id', $product->id)
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
            return response()->json(['message' => __('No user found with that email address.')], 404);
        }

        $product = Product::find($validated['product_id']);

        if (! $product) {
            return response()->json(['message' => __('Product not found.')], 404);
        }

        // Check if user bought any variant of this Lunar Product in a delivered order
        $canReview = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->whereHas('lines', function ($query) use ($product) {
                $query->whereHasMorph('purchasable', [ProductVariant::class], function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                });
            })
            ->exists();

        if (! $canReview) {
            return response()->json(['message' => __('You can only review products from a delivered order.')], 403);
        }

        $hasAlreadyReviewed = ProductReview::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasAlreadyReviewed) {
            return response()->json(['message' => __('You have already submitted a review for this product.')], 409);
        }

        $review = ProductReview::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_approved' => false, // Set to false for moderation
        ]);

        return response()->json([
            'message' => __('Thank you for your review! It is pending approval.'),
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
