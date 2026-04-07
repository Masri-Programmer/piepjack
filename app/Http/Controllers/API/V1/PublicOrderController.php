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

        if (!$user) {
            return response()->json(['message' => 'No user found with that email address.'], 404);
        }

        // Lunar orders belong to a user_id
        // We eager load the lines, the purchased variant (purchasable), and the parent product
        $orders = Order::where('user_id', $user->id)
            ->with(['lines.purchasable.product'])
            ->get();

        return response()->json(PublicOrderListResource::collection($orders));
    }

    public function show(Request $request, string $orderNumber): JsonResponse|PublicOrderListResource
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $order = Order::with([
            'lines.purchasable.product',
            'lines.purchasable.media',
            'currency',
            'shippingAddress.country',
            'user',
            'shippingAddress',
            'billingAddress',
        ])
            ->where('reference', $orderNumber)
            ->first();

        // Check if order exists
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Check if the email matches either the User account OR the Billing Address
        // (Crucial for guest checkouts where order->user might be null)
        $providedEmail = $request->query('email');
        $userEmail = $order->user?->email;
        $billingEmail = $order->billingAddress?->contact_email;

        if ($userEmail !== $providedEmail && $billingEmail !== $providedEmail) {
            return response()->json(['message' => 'The provided email does not match this order.'], 403);
        }

        return new PublicOrderListResource($order);
    }
}
