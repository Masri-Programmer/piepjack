<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        // Collect order status counts
        $orderStatusCounts = Order::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $productCounts = [
            'active' => Product::where('active', true)->count(),
            'inactive' => Product::where('active', false)->count(),
        ];

        $userCounts = [
            'active' => User::where('active', true)->count(),
            'inactive' => User::where('active', false)->count(),
        ];

        $categoryCounts = [
            'active' => Category::where('active', true)->count(),
            'inactive' => Category::where('active', false)->count(),
        ];
        $totalIncome = Order::where('status', 'paid')->sum('total_price');

        return response()->json([
            'order_status_counts' => $orderStatusCounts,
            'product_counts' => $productCounts,
            'user_counts' => $userCounts,
            'category_counts' => $categoryCounts,
            'total_income' => $totalIncome,
        ]);
    }
}
