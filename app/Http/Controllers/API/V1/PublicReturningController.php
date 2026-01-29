<?php

namespace App\Http\Controllers\API\V1;

use Exception;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\User;
use App\Models\Returning;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Models\OrderProduct;
use App\Models\Address;
use Illuminate\Validation\Rule;
use App\Mail\ReturnConfirmation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
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

        $order = Order::with('user')->findOrFail($validated['order_id']);
        $user = $order->user;

        if (!$user->active) {
            return $this->forbiddenResponse('You are banned from using this website.');
        }

        // FIX: Ensure the email matches the order's user directly
        if ($user->email !== $validated['email']) {
            return $this->unprocessableEntityResponse('The provided email does not match the user associated with this order.');
        }
        if (!in_array($order->status, ["paid", "shipped", "delivered"])) {
            return $this->unprocessableEntityResponse('Returns can only be created for paid, shipped, or delivered orders.');
        }

        $return = Returning::updateOrCreate(
            ['order_id' => $validated['order_id']],
            [
                'status' => $validated['status'],
                'reason' => $validated['reason'],
            ]
        );

        foreach ($validated['items'] as $item) {
            if (!$this->canAddReturnItem($return, $item)) {
                continue;
            }
            ReturnItem::create([
                'return_id' => $return->id,
                'product_item_id' => $item['id'],
                'quantity' => $item['cartQuantity'],
            ]);
        }

        try {
            $checkoutSession = $this->createStripeSession($order, $return, $validated['total'], $user->email);
            return $this->successResponse('Return record created successfully. Redirect to payment.', [
                'data' => $return->load('items'),
                'checkout_url' => $checkoutSession->url,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create Stripe session: ' . $e->getMessage());
            return $this->serverErrorResponse('Failed to create Stripe session.', $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook()
    {
        $endpointSecret = config('services.stripe.webhook_return_secret');
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;

        try {
            $event = StripeWebhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return $this->badRequestResponse('Invalid payload.');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return $this->badRequestResponse('Invalid signature.');
        }

        $metadata = $event->data->object->metadata ?? [];
        $orderId = $metadata['order_id'] ?? null;
        $returnId = $metadata['return_id'] ?? null;

        if (!$orderId || !$returnId) {
            return $this->badRequestResponse('Missing required metadata.');
        }

        $order = Order::find($orderId);
        $return = Returning::find($returnId);

        if (!$order || !$return) {
            return $this->notFoundResponse('Order or Return not found.');
        }

        if ($order->status !== 'paid') {
            return $this->okResponse('Order not paid.');
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $return->update(['status' => 'approved']);
                try {
                    $this->sendReturnConfirmationEmail($order, $return);
                } catch (Exception $e) {
                    Log::error("Failed to send return confirmation email for return ID {$return->id}: " . $e->getMessage());
                }
                break;

            case 'checkout.session.async_payment_failed':
            case 'checkout.session.expired':
                $return->update(['status' => 'canceled']);
                ReturnItem::where('return_id', $return->id)->delete();
                break;

            default:
                return response()->json(['message' => 'Unhandled event type'], 400);
        }

        return $this->okResponse('Webhook handled successfully.');
    }

    /**
     * Validate the request data.
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'total' => 'required|numeric',
            'order_id' => 'required|exists:orders,id',
            'email' => 'required|email|exists:users,email',
            'status' => 'required|in:not_requested,requested,approved,rejected',
            'reason' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.id' => [
                'required',
                Rule::exists('order_products', 'product_item_id')->where('order_id', $request->order_id)
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

        $successUrl = config('services.frontend_url') . '/return-order/success?return_number=' . $return->return_number;
        $cancelUrl = config('services.frontend_url') . '/return-order';

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
                ]
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
            'items.productItem.options.variation'
        ]);

        $order->load('user', 'shippingAddress');
        $user = $order->user;
        $address = $order->shippingAddress;

        if (!$user || !$address) {
            Log::error("User or Shipping Address not found for Order ID: {$order->id}");
            return;
        }

        $productItemIds = $return->items->pluck('product_item_id');

        $orderProducts = OrderProduct::where('order_id', $order->id)
            ->whereIn('product_item_id', $productItemIds)
            ->get()
            ->keyBy('product_item_id');

        $items = $return->items->map(function ($returnItem) use ($orderProducts) {
            $productItem = $returnItem->productItem;
            $orderProduct = $orderProducts->get($returnItem->product_item_id);

            return [
                'product_name' => $productItem->product->name,
                'quantity' => $returnItem->quantity,
                'price_per_item' => $orderProduct->price_per_item ?? null,
                'image' => $productItem->image ?? asset('images/logo_new_gray_bg_black.jpeg'),
                'options' => $productItem->options->map(fn($option) => [
                    'name' => optional($option->variation)->name,
                    'value' => $option->value,
                ])->toArray(),
            ];
        });

        $returnData = [
            'return' => $return,
            'user' => $user,
            'address' => $address,
            'items' => $items
        ];

        Mail::to($user->email)->send(new ReturnConfirmation($returnData));
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
        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        $this->sendReturnConfirmationEmail($order, $return);

        return response()->json(['message' => 'Test return email sent successfully']);
    }
}
