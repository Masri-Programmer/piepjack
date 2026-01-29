<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductReview; 
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        if (!$user) {
            return response()->json(['message' => 'No user found with that email address.'], 404);
        }

        $product = Product::find($validated['product_id']);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $canReview = $user->orders()
            ->where('status', 'delivered')
            ->whereHas('products.productItem.product', function ($query) use ($product) {
                $query->where('id', $product->id);
            })
            ->exists();

        if (!$canReview) {
            return response()->json([
                'message' => 'You can only review products from a delivered order.'
            ], 403);
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

        $review->load('user:id,first_name,last_name');

        return response()->json([
            'message' => 'Thank you for your review! It is pending approval.',
            'review' => $review
        ], 201);
    }


    public function show(ProductReview $review): JsonResponse
    {
        $review->load('user:id,first_name,last_name');
        return response()->json($review);
    }

    /**
     * Update the specified review in storage.
     */
    // public function update(UpdateProductReviewRequest $request, ProductReview $review): JsonResponse
    // {
    //     // Use a policy or gate to ensure the user owns the review
    //     $this->authorize('update', $review);

    //     $validated = $request->validated();

    //     $review->update([
    //         'rating' => $validated['rating'],
    //         'title' => $validated['title'] ?? null,
    //         'comment' => $validated['comment'],
    //         // When a review is edited, it should be re-approved by a moderator.
    //         'is_approved' => false,
    //     ]);

    //     $review->load('user:id,name');

    //     return response()->json([
    //         'message' => 'Your review has been updated and is pending re-approval.',
    //         'review' => $review
    //     ]);
    // }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(ProductReview $review): JsonResponse
    {
        // Use a policy or gate to ensure the user owns the review
        // $this->authorize('delete', $review);

        $review->delete();

        return response()->json(null, 204); // 204 No Content is a common response for successful deletion
    }
}
