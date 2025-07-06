<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
// use App\Http\Requests\UpdateProductReviewRequest; // You'll need to create this request class
use App\Models\Product;
use App\Models\ProductReview; // Import the ProductReview model
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    // public function __construct()
    // {
    //     // Protect all methods except 'index' and 'show' with auth middleware
    //     // A user must be authenticated to create, update, or delete a review
    //     $this->middleware('auth:sanctum')->except(['index', 'show']);
    // }

    /**
     * Display a paginated list of approved reviews for a specific product.
     */
    public function index(Product $product): JsonResponse
    {
        $reviews = $product->reviews()
            ->where('is_approved', true) // Only show approved reviews to the public
            ->with('user:id,name')      // Eager load user's id and name to prevent N+1 queries
            ->latest()                  // Order by most recent
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Store a newly created review in storage.
     * The review will be pending approval by default.
     */
    public function store(StoreProductReviewRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        $review = $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            // All new reviews require moderation.
            'is_approved' => false,
        ]);

        // Eager load the user for the response so the frontend can display it immediately
        $review->load('user:id,name');

        return response()->json([
            'message' => 'Thank you for your review! It is pending approval.',
            'review' => $review
        ], 201);
    }

    /**
     * Display the specified review.
     */
    public function show(ProductReview $review): JsonResponse
    {
        // You might want to add authorization here if reviews can be private
        // For now, we'll assume any approved review can be seen.
        // if (!$review->is_approved && auth()->id() !== $review->user_id) {
        //     abort(404); // Or 403 Forbidden
        // }

        $review->load('user:id,name');
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
