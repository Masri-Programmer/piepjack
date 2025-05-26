<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Order::filter(request()->only(['status', 'search']), ['id', 'order_number'])
            ->sort([
                request('sort_field', 'created_at') => request('sort_direction', 'desc'),
            ])
            ->paginateResults(request('per_page', 10));

        return OrderListResource::collection($query)
            ->additional([
                'meta' => [
                    'statuses' => Order::getStatusOptions(),
                ],
            ])
            ->response();
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json(
            new OrderResource(
                $order->load('customerDetail.customer', 'products.productItem.product', 'products.productItem.options.variation', 'returning.items')
            )
        );
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'total_price' => 'nullable',
            'status' => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json($order);
    }

    public function destroy(Order $Order)
    {
        $Order->delete();

        return response('');
    }
}
