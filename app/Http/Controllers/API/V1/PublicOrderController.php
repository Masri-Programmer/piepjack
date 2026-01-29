<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\PublicOrderListResource;
use Illuminate\Http\JsonResponse;

class PublicOrderController extends Controller
{
    /**
     * Display a listing of orders for a given email.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->query('email'))->first();

        if (!$user) {
            return response()->json(['message' => 'No user found with that email address.'], 404);
        }

        $orders = $user->orders()->with('products')->get();

        return response()->json(OrderListResource::collection($orders));
    }

    /**
     * Display the specified order.
     */
    public function show(string $orderNumber): PublicOrderListResource
    {
        $order = Order::with([
            'products.productItem.product',
            'user',
            'shippingAddress',
            'billingAddress',
        ])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return new PublicOrderListResource($order);
    }
}
