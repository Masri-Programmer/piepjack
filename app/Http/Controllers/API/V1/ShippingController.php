<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\SendcloudService;
use Illuminate\Http\Request;
use Lunar\DataTypes\Price;
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

        $order = Order::with(['user', 'shippingAddress.country', 'shippingLines', 'lines'])->findOrFail($validated['order_id']);

        if (! $order->user || $order->user->email !== $validated['email']) {
            return response()->json(['error' => __('Unauthorized access to this order.')], 403);
        }

        if (! $order->shippingAddress) {
            return response()->json(['error' => __('Incomplete order customer data.')], 400);
        }

        // Safely load purchasable only for physical lines
        $order->lines->where('type', 'physical')->load('purchasable.product');

        // Format customer data for Sendcloud
        $customerData = [
            'name' => $order->user->first_name.' '.$order->user->last_name,
            'address' => $order->shippingAddress->line_one,
            'city' => $order->shippingAddress->city,
            'zip' => $order->shippingAddress->postcode,
            'country_code' => $order->shippingAddress->country->iso2 ?? config('shop.default_country'),
            'email' => $order->user->email,
        ];

        // 2. Extract and format physical items for Sendcloud
        $productLines = $order->lines->filter(fn ($line) => $line->type === 'physical');

        $parcelItems = $productLines->map(function ($line) {
            $name = $line->description;
            if ($line->purchasable && $line->purchasable->product) {
                $name = $line->purchasable->product->translateAttribute('name');
            }

            return [
                'description' => $name,
                'quantity' => (int) $line->quantity,
                'weight' => '0.1', // Simple hardcoded dummy weight per item
                'value' => $line->unit_price instanceof Price ? $line->unit_price->decimal : (float) ($line->unit_price / 100),
            ];
        })->values()->toArray();

        $totalWeight = 1.0;

        $shippingLine = $order->shippingLines->first();
        $shippingMethodId = config('services.sendcloud.default_method_id');
        if ($shippingLine && isset($shippingLine->meta['sendcloud_id'])) {
            $shippingMethodId = $shippingLine->meta['sendcloud_id'];
        }

        try {
            $shippingResult = $this->sendcloud->createParcel($customerData, $totalWeight, (int) $shippingMethodId, true, $parcelItems);

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
