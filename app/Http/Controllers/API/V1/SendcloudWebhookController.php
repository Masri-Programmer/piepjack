<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\OrderDelivered;
use App\Mail\OrderShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Order;

class SendcloudWebhookController extends Controller
{
    /**
     * Handle the Sendcloud webhook for parcel status updates.
     */
    public function handle(Request $request)
    {
        // 1. Verify Sendcloud Signature
        $signature = $request->header('Sendcloud-Signature');
        $secret = config('services.sendcloud.webhook_secret') ?: config('services.sendcloud.secret_key'); // Fallback for backward compatibility while testing
        $rawPayload = $request->getContent();

        if (empty($secret)) {
            Log::error('Sendcloud webhook: Secret key is not configured properly.');

            return response()->json(['message' => 'Internal server error'], 500);
        }

        // Sendcloud sends the signature as a SHA256 HMAC of the raw payload using the secret key
        $hash = hash_hmac('sha256', $rawPayload, $secret);
        if (! hash_equals($hash, $signature ?? '')) {
            Log::warning('Sendcloud webhook: invalid signature detected.');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 2. Decode Payload
        $payload = json_decode($rawPayload, true);
        if (! $payload || ! isset($payload['parcel'])) {
            return response()->json(['message' => 'Invalid payload structure'], 400);
        }

        $action = $payload['action'] ?? 'unknown';
        $parcel = $payload['parcel'];
        $trackingNumber = $parcel['tracking_number'] ?? null;
        $statusId = $parcel['status']['id'] ?? null;
        $statusMessage = $parcel['status']['message'] ?? '';

        Log::info("Sendcloud Webhook [{$action}]: Tracking {$trackingNumber}, Status ID {$statusId} ({$statusMessage})");

        if (! $trackingNumber) {
            return response()->json(['message' => 'Missing tracking number'], 400);
        }

        $parcelId = $parcel['id'] ?? null;

        // 3. Find Order by Parcel ID or Tracking Number
        $order = Order::where('meta->sendcloud_parcel_id', $parcelId)
            ->orWhere(function ($query) use ($trackingNumber) {
                if ($trackingNumber) {
                    $query->where('tracking_number', $trackingNumber);
                }
            })->first();

        if (! $order) {
            // It might be that the label was just created and the tracking number is not yet in our DB,
            // and the parcel ID doesn't match either.
            Log::info("Sendcloud webhook: Order not found for parcel {$parcelId} or tracking {$trackingNumber}");

            return response()->json(['status' => 'order_not_found'], 200);
        }

        // If the order was generated unannounced, it won't have a tracking number yet. Let's save it.
        if (empty($order->tracking_number) && $trackingNumber) {
            $currentMeta = (array) ($order->meta ?? []);
            $order->update([
                'tracking_number' => $trackingNumber,
                'meta' => array_merge($currentMeta, ['tracking_number' => $trackingNumber]),
            ]);
            Log::info("Order {$order->reference} tracking number initialized via webhook to {$trackingNumber}.");
        }

        // 4. Update Order Status based on Sendcloud Status IDs
        // Mapping Sendcloud Statuses to Lunar Statuses
        // Ref: https://www.sendcloud.dev/docs/shipping/parcel-statuses
        switch ($statusId) {
            case 14: // Delivered
            case 15: // Delivered to service point
                if ($order->status !== 'delivered') {
                    $order->update(['status' => 'delivered']);
                    Log::info("Order {$order->reference} status updated to 'delivered'.");

                    try {
                        Mail::to($order->user->email)->send(new OrderDelivered($order));
                        Log::info("Delivered notification sent for order {$order->reference}.");
                    } catch (\Exception $e) {
                        Log::error("Failed to send delivered notification for order {$order->reference}: ".$e->getMessage());
                    }
                }
                break;

            case 11: // In transit
            case 12: // At sorting center
            case 13: // Being delivered
            case 3:  // In transit (another variation)
                if (! in_array($order->status, ['shipped', 'delivered'])) {
                    $order->update(['status' => 'shipped']);
                    Log::info("Order {$order->reference} status updated to 'shipped'.");

                    try {
                        Mail::to($order->user->email)->send(new OrderShipped($order));
                        Log::info("Shipped notification sent for order {$order->reference}.");
                    } catch (\Exception $e) {
                        Log::error("Failed to send shipped notification for order {$order->reference}: ".$e->getMessage());
                    }
                }
                break;

            case 1000: // Ready to send (Announced)
            case 1001: // Label printed
                if (! in_array($order->status, ['shipped', 'delivered', 'dispatched'])) {
                    $order->update(['status' => 'dispatched']);
                    Log::info("Order {$order->reference} status updated to 'dispatched'.");
                }
                break;

            case 2000: // Return received
            case 2001: // Return in transit
                // Handle returns if needed
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }
}
