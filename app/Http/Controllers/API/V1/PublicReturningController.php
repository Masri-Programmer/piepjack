<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\AdminReturnNotification;
use App\Mail\ReturnConfirmation;
use App\Models\Returning;
use App\Models\ReturnItem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Lunar\Models\Order;
use Lunar\Models\Product;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook as StripeWebhook;

class PublicReturningController extends Controller
{
    /**
     * Show the specified return request.
     */
    public function show($id): JsonResponse
    {
        $return = Returning::with('order')->findOrFail($id);

        return response()->json($return);
    }

    /**
     * Store a new return request.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request);

        $order = Order::with(['user', 'billingAddress'])->findOrFail($validated['order_id']);

        // Check email against billing address or user email
        $orderEmail = $order->billingAddress?->contact_email ?? $order->user?->email;

        if ($orderEmail !== $validated['email']) {
            return $this->unprocessableEntityResponse(__('The provided email does not match the email associated with this order.'));
        }

        if (! in_array($order->status, ['payment-received', 'dispatched'])) {
            return $this->unprocessableEntityResponse(__('Returns can only be created for paid, shipped, or delivered orders.'));
        }

        // Create the RMA record
        $return = Returning::updateOrCreate(
            ['order_id' => $validated['order_id']],
            [
                'status' => 'requested',
                'reason' => $validated['reason'],
                'return_fee' => $validated['total'],
            ]
        );

        foreach ($validated['items'] as $item) {
            if (! $this->canAddReturnItem($return, $item)) {
                continue;
            }
            ReturnItem::updateOrCreate(
                ['return_id' => $return->id, 'product_item_id' => $item['id']],
                ['quantity' => $item['cartQuantity']]
            );
        }

        // 🔹 FIX 2: Generate Sendcloud Label & Send Email Immediately
        try {
            $order->load('shippingAddress.country');
            $this->generateSendcloudReturnLabel($order, $return);
            $this->sendReturnConfirmationEmail($order->fresh(), $return->fresh());
        } catch (Exception $e) {
            Log::error("Failed to finalize return #{$return->id}: ".$e->getMessage());
        }

        return $this->successResponse(__('Return request submitted successfully.'), [
            'data' => $return->load('items'),
        ]);
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (! $sigHeader) {
            return $this->badRequestResponse(__('Missing Stripe-Signature header.'));
        }

        try {
            $event = StripeWebhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_return_secret')
            );
        } catch (\UnexpectedValueException $e) {
            return $this->badRequestResponse(__('Invalid payload.'));
        } catch (SignatureVerificationException $e) {
            return $this->badRequestResponse(__('Invalid signature.'));
        }

        $metadata = $event->data->object->metadata ?? [];
        $orderId = $metadata['order_id'] ?? null;
        $returnId = $metadata['return_id'] ?? null;

        if (! $orderId || ! $returnId) {
            return $this->badRequestResponse(__('Missing required metadata.'));
        }

        $order = Order::find($orderId);
        $return = Returning::find($returnId);

        if (! $order || ! $return) {
            return $this->notFoundResponse(__('Order or Return not found.'));
        }

        if ($order->status !== 'paid') {
            return $this->okResponse(__('Order not paid.'));
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $return->update(['status' => 'approved']);
                try {
                    $this->sendReturnConfirmationEmail($order, $return);
                } catch (Exception $e) {
                    Log::error("Failed to send return confirmation email for return ID {$return->id}: ".$e->getMessage());
                }
                break;

            case 'checkout.session.async_payment_failed':
            case 'checkout.session.expired':
                $return->update(['status' => 'canceled']);
                ReturnItem::where('return_id', $return->id)->delete();
                break;

            default:
                return response()->json(['message' => __('Unhandled event type')], 400);
        }

        return $this->okResponse(__('Webhook handled successfully.'));
    }

    /**
     * Validate the request data.
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'total' => 'required|numeric',
            'order_id' => 'required|exists:lunar_orders,id',
            'email' => 'required|email', // Removed exists:users,email as guest orders exist
            'status' => 'required|in:not_requested,requested,approved,rejected',
            'reason' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.id' => [
                'required',
                Rule::exists('lunar_order_lines', 'purchasable_id')->where('order_id', $request->order_id),
            ],
            'items.*.cartQuantity' => 'required|integer|min:1',
        ]);
    }

    /**
     * Check if a return item can be added.
     */
    private function canAddReturnItem(Returning $return, array $item): bool
    {
        $existingReturnItem = ReturnItem::where('product_item_id', $item['id'])
            ->where('return_id', $return->id)
            ->first();

        if ($existingReturnItem && $return->status === 'approved') {
            $this->unprocessableEntityResponse('Some products have already been returned. Please review your return request.', [
                'product_id' => $item['id'],
            ]);

            return false;
        }

        return true;
    }

    /**
     * Create a Stripe checkout session.
     */
    private function createStripeSession(Order $order, Returning $return, float $total, string $email): Session
    {

        $successUrl = config('services.frontend_url').'/return-order/success?return_number='.$return->return_number;
        $cancelUrl = config('services.frontend_url').'/return-order';

        return Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Delivery Fee for Return',
                        ],
                        'unit_amount' => (int) round($total * 100),
                    ],
                    'quantity' => 1,
                ],
            ],
            'payment_method_types' => config('services.stripe.payment_methods'),
            'mode' => 'payment',
            'metadata' => [
                'order_id' => $order->id,
                'return_id' => $return->id,
                'total_price' => $total,
                'payment_type' => 'return_items',
                'email' => $email,
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    /**
     * Send return confirmation email.
     */
    private function sendReturnConfirmationEmail(Order $order, Returning $return)
    {
        $return->load([
            'items.productItem.product',
            'items.productItem.values.option',
        ]);

        $order->load('user', 'shippingAddress.country', 'billingAddress');
        $user = $order->user;
        $address = $order->shippingAddress;

        // Use user email or fallback to billing address email
        $recipientEmail = $user?->email ?? $order->billingAddress?->contact_email;

        if (! $recipientEmail) {
            Log::error("No recipient email found for Order ID: {$order->id}");

            return;
        }

        if (! $address) {
            Log::error("Shipping Address not found for Order ID: {$order->id}");

            return;
        }

        $productItemIds = $return->items->pluck('product_item_id');

        // Lunar orders have OrderLines, not a direct Product link with order_id
        $orderLines = $order->lines->whereIn('purchasable_id', $productItemIds)->keyBy('purchasable_id');

        $items = $return->items->map(function ($returnItem) use ($orderLines) {
            $productVariant = $returnItem->productItem; // This is a ProductVariant
            $line = $orderLines->get($returnItem->product_item_id);

            return [
                'name' => (string) $productVariant->product->translateAttribute('name'),
                'quantity' => $returnItem->quantity,
                'price_per_item' => $line?->unit_price?->decimal ?? 0,
                'image' => $productVariant->product->thumbnail?->getUrl() ?? config('services.branding.logo_url'),
                'options' => $productVariant->values->map(fn ($value) => [
                    'name' => (string) ($value->option?->translate('name') ?? ''),
                    'value' => (string) ($value->translate('name') ?? ''),
                ])->toArray(),
            ];
        });

        $returnData = [
            'return' => $return,
            'user' => $user ?? (object) ['first_name' => $order->billingAddress->first_name ?? __('Customer')],
            'address' => $address,
            'items' => $items,
        ];

        try {
            // Send to User
            Mail::to($recipientEmail)->send(new ReturnConfirmation($returnData));

            // Send to Admin
            if (config('app.admin_email')) {
                Mail::to(config('app.admin_email'))->send(new AdminReturnNotification($returnData));
            }
        } catch (Exception $e) {
            Log::error("Failed to send return confirmation email for return ID {$return->id}: ".$e->getMessage());
        }
    }

    /**
     * Helper methods for standardized responses.
     */
    private function successResponse(string $message, array $data = []): JsonResponse
    {
        return response()->json(array_merge(['message' => $message], $data), 201);
    }

    private function forbiddenResponse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 403);
    }

    private function unprocessableEntityResponse(string $message, array $data = []): JsonResponse
    {
        return response()->json(array_merge(['message' => $message], $data), 422);
    }

    private function serverErrorResponse(string $message, string $error = ''): JsonResponse
    {
        return response()->json(['message' => $message, 'error' => $error], 500);
    }

    private function badRequestResponse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 400);
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 404);
    }

    private function okResponse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 200);
    }

    /**
     * Test function for sending a return email.
     */
    public function sendReturnEmailTest($returnId)
    {
        $return = Returning::findOrFail($returnId);

        // FIX: Load order with the necessary relationships
        $order = Order::with(['user', 'shippingAddress'])->find($return->order_id);
        if (! $order) {
            return $this->notFoundResponse('Order not found');
        }

        $this->sendReturnConfirmationEmail($order, $return);

        return response()->json(['message' => 'Test return email sent successfully']);
    }

    /**
     * Generate a return label via Sendcloud API.
     */
    private function generateSendcloudReturnLabel(Order $order, Returning $return): void
    {
        $publicKey = config('services.sendcloud.public_key');
        $secretKey = config('services.sendcloud.secret_key');
        $returnMethodId = config('services.sendcloud.default_return_method_id');

        if (! $publicKey || ! $secretKey) {
            Log::warning('Sendcloud credentials not configured.');

            return;
        }

        $address = $order->shippingAddress;

        try {
            // Sendcloud API requires "from_" fields when is_return is true
            $response = Http::withBasicAuth($publicKey, $secretKey)
                ->post('https://panel.sendcloud.sc/api/v2/parcels', [
                    'parcel' => [
                        'from_name' => "{$address->first_name} {$address->last_name}",
                        'from_address_1' => $address->line_one,
                        'from_house_number' => $address->house_number ?? '',
                        'from_city' => $address->city,
                        'from_postal_code' => $address->postcode,
                        'from_country' => $address->country->iso2,
                        'from_email' => $address->contact_email,
                        'is_return' => true,
                        'request_label' => true,
                        'return_method' => (int) $returnMethodId,
                        'external_order_id' => $order->reference,
                        'weight' => '1.000',
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json()['parcel'];
                $return->update([
                    'sendcloud_return_id' => $data['id'],
                    'label_url' => $data['label']['label_printer'] ?? null,
                    'qr_code_url' => $data['label']['qr_code'] ?? null,
                ]);
            } else {
                Log::error('Sendcloud Return Label Error: '.$response->body());
            }
        } catch (Exception $e) {
            Log::error('Sendcloud Return Label Exception: '.$e->getMessage());
        }
    }
}
