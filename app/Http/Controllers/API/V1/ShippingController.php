<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\SendcloudService;
use Illuminate\Http\Request;
use Lunar\Models\Order;

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
            'order_id' => 'required|exists:lunar_orders,id',
            'email' => 'required|email',
        ]);

        $order = Order::with(['user', 'shippingAddress', 'shippingLines'])->findOrFail($validated['order_id']);

        if (! $order->user || $order->user->email !== $validated['email']) {
            return response()->json(['error' => __('Unauthorized access to this order.')], 403);
        }

        if (! $order->shippingAddress) {
            return response()->json(['error' => __('Incomplete order customer data.')], 400);
        }

        // Format customer data for Sendcloud
        $customerData = [
            'name' => $order->user->first_name.' '.$order->user->last_name,
            'address' => $order->shippingAddress->line_one,
            'city' => $order->shippingAddress->city,
            'zip' => $order->shippingAddress->postcode,
            'country_code' => $order->shippingAddress->country->iso2 ?? 'DE',
            'email' => $order->user->email,
        ];

        // 2. Call the service to create the parcel
        $totalWeight = 1.0; // Placeholder until physical weights exist
        $shippingLine = $order->shippingLines->first();
        $shippingMethodId = $shippingLine && is_numeric($shippingLine->identifier)
            ? (int) $shippingLine->identifier
            : 8;

        try {
            $shippingResult = $this->sendcloud->createParcel($customerData, $totalWeight, $shippingMethodId);

            // 3. Save the tracking number to database
            $currentMeta = (array) ($order->meta ?? []);
            $order->update([
                'tracking_number' => $shippingResult['tracking_number'],
                'label_url' => $shippingResult['label_url'],
                'meta' => array_merge($currentMeta, [
                    'tracking_number' => $shippingResult['tracking_number'],
                    'label_url' => $shippingResult['label_url'],
                ]),
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
