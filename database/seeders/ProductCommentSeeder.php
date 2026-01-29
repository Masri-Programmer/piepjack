<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductComment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProductComment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::pluck('id');
        $products = Product::pluck('id');

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Cannot seed product comments. Please seed users and products first.');
            return;
        }

        $comments = [];

        // Create 150 top-level comments
        for ($i = 0; $i < 150; $i++) {
            $comments[] = [
                'user_id' => $users->random(),
                'product_id' => $products->random(),
                'parent_id' => null,
                'comment' => fake()->paragraph(2),
                'is_approved' => fake()->boolean(90),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Use mass insertion for performance
        ProductComment::insert($comments);

        // --- Create Replies (Threaded Comments) ---
        $parentComments = ProductComment::whereNull('parent_id')->get();
        $replyComments = [];

        if ($parentComments->isNotEmpty()) {
            // Create 100 replies
            for ($i = 0; $i < 100; $i++) {
                $parentComment = $parentComments->random();

                $replyComments[] = [
                    'user_id' => $users->random(),
                    'product_id' => $parentComment->product_id,
                    'parent_id' => $parentComment->id,
                    'comment' => fake()->paragraph(1),
                    'is_approved' => fake()->boolean(90),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        ProductComment::insert($replyComments);
    }
}
