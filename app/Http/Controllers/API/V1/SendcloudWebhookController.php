<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendcloudWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Verify Sendcloud Signature
        $signature = $request->header('Sendcloud-Signature');
        $secret = config('services.sendcloud.secret_key');
        $rawPayload = $request->getContent();

        $hash = hash_hmac('sha256', $rawPayload, $secret);
        if (! hash_equals($hash, $signature ?? '')) {
            Log::warning('Sendcloud webhook invalid signature');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 2. Decode Payload
        $payload = json_decode($rawPayload, true);
        if (! $payload || ! isset($payload['parcel'])) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $trackingNumber = $payload['parcel']['tracking_number'] ?? null;
        $statusMessage = $payload['parcel']['status']['message'] ?? '';

        if (! $trackingNumber) {
            return response()->json(['message' => 'No tracking number'], 400);
        }

        // 3. Find Order and Map Statuses
        $order = Order::where('tracking_number', $trackingNumber)->first();

        if ($order) {
            if ($statusMessage === 'Delivered') {
                $order->update(['status' => 'delivered']);
                Log::info("Order with tracking {$trackingNumber} was delivered!");
                // Optionally trigger Delivered email
            } elseif (in_array($statusMessage, ['Announced', 'In transit', 'Ready to send'])) {
                // We only update to shipped if it's currently paid or pending, to not revert delivery
                if (in_array($order->status, ['paid', 'pending'])) {
                    $order->update(['status' => 'shipped']);
                    Log::info("Order with tracking {$trackingNumber} is in transit.");
                }
            }
        } else {
            Log::warning("Sendcloud webhook: Order not found for tracking {$trackingNumber}");
        }

        // Always return a 200 OK so Sendcloud knows you received the message
        return response()->json(['status' => 'success'], 200);
    }
}
