<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductReview;

class ProductReviewObserver
{
    /**
     * Handle the ProductReview "created" event.
     */
    public function created(ProductReview $productReview): void
    {
        if ($productReview->is_approved) {
            $this->updateProductReviewStats($productReview->product);
        }
    }

    /**
     * Handle the ProductReview "updated" event.
     */
    public function updated(ProductReview $productReview): void
    {
        // Only recalculate if the approval status has changed.
        if ($productReview->isDirty('is_approved')) {
            $this->updateProductReviewStats($productReview->product);
        }
    }

    /**
     * Handle the ProductReview "deleted" event.
     */
    public function deleted(ProductReview $productReview): void
    {
        $this->updateProductReviewStats($productReview->product);
    }

    /**
     * Handle the ProductReview "restored" event.
     */
    public function restored(ProductReview $productReview): void
    {
        $this->updateProductReviewStats($productReview->product);
    }

    /**
     * Recalculate and update the review statistics for a given product.
     */
    protected function updateProductReviewStats(Product $product): void
    {
        // We only want to calculate stats for approved reviews.
        $approvedReviews = $product->reviews()->where('is_approved', true);

        $product->reviews_count = $approvedReviews->count();
        $product->reviews_avg_rating = $approvedReviews->avg('rating');

        // Use saveQuietly() to prevent triggering other model events on the Product model.
        $product->saveQuietly();
    }
}
