<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryNames = ['t-shirts', 'sweaters', 'jackets', 'sports', 'accessories', 'underwear'];

        foreach ($categoryNames as $name) {
            Category::create(['name' => ucfirst($name), 'active' => true]);
        }
    }
}
