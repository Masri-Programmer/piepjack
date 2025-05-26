<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
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

        $customerCounts = [
            'active' => Customer::where('active', true)->count(),
            'inactive' => Customer::where('active', false)->count(),
        ];

        $categoryCounts = [
            'active' => Category::where('active', true)->count(),
            'inactive' => Category::where('active', false)->count(),
        ];
        $totalIncome = Order::where('status', 'paid')->sum('total_price');

        return response()->json([
            'order_status_counts' => $orderStatusCounts,
            'product_counts' => $productCounts,
            'customer_counts' => $customerCounts,
            'category_counts' => $categoryCounts,
            'total_income' => $totalIncome,
        ]);
    }
}
