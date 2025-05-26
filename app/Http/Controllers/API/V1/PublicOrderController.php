<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\PublicOrderListResource;

class PublicOrderController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json(['message' => 'Email is required'], 400);
        }

        $customer = Customer::where('email', $email)
            ->with(['details.orders.products'])
            ->first();

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $orders = $customer->details?->orders ?? collect();

        return response()->json(OrderListResource::collection($orders));
    }

    public function show($orderNumber)
    {
        $order = Order::with([
            'products.productItem.configurations.variationOption.variation',
            'customerDetail',
        ])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return new PublicOrderListResource($order);
    }
}
