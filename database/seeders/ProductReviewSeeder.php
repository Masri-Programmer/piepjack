<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // It's good practice to truncate the table before seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProductReview::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Fetch existing users and products to associate reviews with
        $users = User::pluck('id');
        $products = Product::pluck('id');

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Cannot seed product reviews. Please seed users and products first.');
            return;
        }

        $reviews = [];
        $reviewedCombinations = [];

        // Create 200 reviews as an example
        for ($i = 0; $i < 200; $i++) {
            $userId = $users->random();
            $productId = $products->random();
            $combination = $userId . '-' . $productId;

            // Skip if this user has already reviewed this product to respect the unique constraint
            if (in_array($combination, $reviewedCombinations)) {
                continue;
            }

            $reviews[] = [
                'user_id' => $userId,
                'product_id' => $productId,
                'rating' => fake()->numberBetween(1, 5),
                'title' => fake()->boolean(70) ? fake()->sentence(4) : null, // 70% chance of having a title
                'comment' => fake()->paragraph(3),
                'is_approved' => fake()->boolean(90), // 90% chance of being approved
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $reviewedCombinations[] = $combination;
        }

        // Insert reviews in chunks for better performance
        foreach (array_chunk($reviews, 100) as $chunk) {
            ProductReview::insert($chunk);
        }

        // After seeding reviews, update the aggregate columns on the products table
        $this->updateProductReviewStats();
    }

    /**
     * Update the review count and average rating for each product.
     */
    protected function updateProductReviewStats(): void
    {
        $products = Product::whereHas('reviews')->with('reviews')->get();

        foreach ($products as $product) {
            $approvedReviews = $product->reviews()->where('is_approved', true);

            $product->update([
                'reviews_count' => $approvedReviews->count(),
                'reviews_avg_rating' => $approvedReviews->avg('rating'),
            ]);
        }
    }
}
