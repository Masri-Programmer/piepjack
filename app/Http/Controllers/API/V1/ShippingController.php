<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SendcloudService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected SendcloudService $sendcloud;

    public function __construct(SendcloudService $sendcloud)
    {
        $this->sendcloud = $sendcloud;
    }

    /**
     * Handle the label generation after a successful order.
     */
    public function generateLabel(Request $request)
    {
        // 1. Validate the incoming data from Vue
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with(['user', 'shippingAddress'])->findOrFail($validated['order_id']);

        if (! $order->shippingAddress || ! $order->user) {
            return response()->json(['error' => __('Incomplete order customer data.')], 400);
        }

        // Format customer data for Sendcloud
        $customerData = [
            'name' => $order->user->first_name.' '.$order->user->last_name,
            'address' => $order->shippingAddress->street_address,
            'city' => $order->shippingAddress->city,
            'zip' => $order->shippingAddress->postal_code,
            'country_code' => $order->shippingAddress->country_code,
            'email' => $order->user->email,
        ];

        // 2. Call the service to create the parcel
        $totalWeight = 1.0; // Placeholder until physical weights exist
        $shippingMethodId = $order->shipping_method_id ?? 8;

        try {
            $shippingResult = $this->sendcloud->createParcel($customerData, $totalWeight, $shippingMethodId);

            // 3. Save the tracking number to database
            $order->update([
                'tracking_number' => $shippingResult['tracking_number'],
                'label_url' => $shippingResult['label_url'],
            ]);

            // 4. Return the data
            return response()->json([
                'message' => __('Label generated successfully!'),
                'tracking_number' => $shippingResult['tracking_number'],
                'label_url' => $shippingResult['label_url'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('Failed to generate shipping label: ').$e->getMessage()], 500);
        }
    }
}
