<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicOrderListResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lunar\Models\Order;

class PublicOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->query('email'))->first();

        if (! $user) {
            return response()->json(['message' => __('No user found with that email address.')], 404);
        }

        // Lunar orders belong to a user_id
        // We eager load the lines, the purchased variant (purchasable), and the parent product
        $orders = Order::where('user_id', $user->id)
            ->with(['lines.purchasable.product'])
            ->get();

        return response()->json(PublicOrderListResource::collection($orders));
    }

    public function show(Request $request, $order): JsonResponse|PublicOrderListResource
    {
        $request->validate(['email' => 'required|email']);

        $reference = is_object($order) ? $order->reference : $order;

        // Load only the solid Model relationships
        $orderData = Order::with([
            'lines',
            'currency',
            'shippingAddress.country',
            'user',
            'billingAddress',
        ])
            ->where('reference', $reference)
            ->first();

        if (! $orderData) {
            return response()->json(['message' => __('Order not found.')], 404);
        }

        // Security check
        $providedEmail = $request->query('email');
        if (
            $orderData->billingAddress?->contact_email !== $providedEmail &&
            $orderData->user?->email !== $providedEmail
        ) {
            return response()->json(['message' => __('Unauthorized.')], 403);
        }

        return new PublicOrderListResource($orderData);
    }
}
