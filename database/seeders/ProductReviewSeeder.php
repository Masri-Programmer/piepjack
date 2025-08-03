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

        // Fetch existing Users and products to associate reviews with
        $Users = User::pluck('id');
        $products = Product::pluck('id');

        if ($Users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Cannot seed product reviews. Please seed Users and products first.');
            return;
        }

        $reviews = [];
        $reviewedCombinations = [];

        // Create 200 reviews as an example
        for ($i = 0; $i < 200; $i++) {
            $UserId = $Users->random();
            $productId = $products->random();
            $combination = $UserId . '-' . $productId;

            if (in_array($combination, $reviewedCombinations)) {
                continue;
            }

            $reviews[] = [
                'user_id' => $UserId,
                'product_id' => $productId,
                'rating' => fake()->numberBetween(1, 5),
                'title' => fake()->boolean(70) ? fake()->sentence(4) : null,
                'comment' => fake()->paragraph(3),
                'is_approved' => fake()->boolean(90),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $reviewedCombinations[] = $combination;
        }

        foreach (array_chunk($reviews, 100) as $chunk) {
            ProductReview::insert($chunk);
        }

        $this->updateProductReviewStats();
    }

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
