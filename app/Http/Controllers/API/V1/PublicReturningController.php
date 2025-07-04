<?php

namespace App\Http\Controllers\API\V1;

use Exception;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Returning;
use App\Models\ReturnItem;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Models\OrderProduct;
use App\Models\CustomerDetail;
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
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $return = Returning::with('order')->findOrFail($id);
        return response()->json($return);
    }

    /**
     * Store a new return request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // Validate input data
        $validated = $this->validateRequest($request);

        // Fetch related entities
        $order = Order::findOrFail($validated['order_id']);
        $customer = Customer::where('email', $validated['email'])->first();

        // Check if the customer is banned
        if ($customer && ! $customer->active) {
            return $this->forbiddenResponse('You are banned from using this website.');
        }

        // Ensure the email matches the order's customer
        if ($order->customerDetail->customer->email !== $validated['email']) {
            return $this->unprocessableEntityResponse('The provided email does not match the customer associated with this order.');
        }

        // Ensure the order is paid
        if ($order->status !== 'paid') {
            return $this->unprocessableEntityResponse('Returns can only be created for paid orders.');
        }

        // Create or update the return request
        $return = Returning::updateOrCreate(
            ['order_id' => $validated['order_id']],
            [
                'status' => $validated['status'],
                'reason' => $validated['reason'],
            ]
        );

        // Process return items
        foreach ($validated['items'] as $item) {
            if (!$this->canAddReturnItem($return, $item)) {
                continue; // Skip duplicates or invalid items
            }
            ReturnItem::create([
                'return_id' => $return->id,
                'product_item_id' => $item['id'],
                'quantity' => $item['cartQuantity'],
            ]);
        }

        Log::info(' Paymnt recieved Total:', ['total' => $validated['total'], 'converted_total' => (int) round($validated['total'] * 100)]);

        // Initialize Stripe payment session
        try {
            $checkoutSession = $this->createStripeSession($order, $return, $validated['total'], $customer->email);
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
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function handleWebhook()
    {
        // cd "C:\Program Files\Stripe CLI\stripe_1.23.5_windows_x86_64"
        // stripe login
        // stripe listen --forward-to localhost:8000/api/V1/shop/webhook/return-items
        // $endpointSecret = 'whsec_1ec88b1f8be092cb44234aa740821ceb154cd06bdd5fe05b996b09ef79d33a94';
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET_RETURN');
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

        if (! $orderId || ! $returnId) {
            return $this->badRequestResponse('Missing required metadata.');
        }

        $order = Order::find($orderId);
        $return = Returning::find($returnId);

        if (! $order) {
            return $this->notFoundResponse('Order not found.');
        }

        if (! $return) {
            return $this->notFoundResponse('Return not found.');
        }

        if ($order->status !== 'paid') {
            return $this->okResponse('Order not paid.');
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $return->update(['status' => 'approved']);
                try {
                    $this->sendReturnConfirmationEmail($order, $return);
                } catch (\Exception $e) {
                    // Email failed, but continue execution
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'total' => 'required',
            'order_id' => 'required|exists:orders,id',
            'email' => 'required|email|exists:customers,email',
            'status' => 'required|in:not_requested,requested,approved,rejected',
            'reason' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.id' => [
                'required',
                Rule::exists('order_products', 'product_item_id')
            ],
            'items.*.cartQuantity' => 'required|integer|min:1',
        ]);
    }

    /**
     * Check if a return item can be added.
     *
     * @param  \App\Models\Returning  $return
     * @param  array  $item
     * @return bool
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
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\Returning  $return
     * @param  int  $total
     * @param  string  $email
     * @return \Stripe\Checkout\Session
     */
    private function createStripeSession(Order $order, Returning $return, float $total, string $email): Session
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $successUrl = env('VITE_FRONTEND_URL') . '/return-order/success?return_number=' . $return->return_number;
        $cancelUrl = env('VITE_FRONTEND_URL') . '/return-order';

        return Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Delivery Fee for Return',
                    ],
                    'unit_amount' => (float) round($total * 100),
                ],
                'quantity' => 1,
            ]],
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
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\Returning  $return
     */
    private function sendReturnConfirmationEmail(Order $order, Returning $return)
    {
        $return->load([
            'items.productItem.product',
            'items.productItem.options.variation'
        ]);

        $customerDetail = CustomerDetail::find($order->customer_details_id);
        if (!$customerDetail) {
            return;
        }

        $customer = Customer::find($customerDetail->customer_id);
        if (!$customer) {
            return;
        }

        $productItemIds = $return->items->pluck('product_item_id');

        $orderProducts = OrderProduct::where('order_id', $order->id)
            ->whereIn('product_item_id', $productItemIds)
            ->get()
            ->keyBy('product_item_id');

        // Map the return items to the desired detailed format for the email
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
            'customer' => $customerDetail,
            'items' => $items
        ];

        Mail::to($customer->email)->send(new ReturnConfirmation($returnData));
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

    public function sendReturnEmailTest($returnId)
    {
        $return = Returning::with([
            'items.productItem.product',
            'items.productItem.options.variation'
        ])->findOrFail($returnId);

        $order = Order::find($return->order_id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $customerDetail = CustomerDetail::find($order->customer_details_id);
        if (!$customerDetail) {
            return response()->json(['message' => 'Customer details not found'], 404);
        }

        $customer = Customer::find($customerDetail->customer_id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $productItemIds = $return->items->pluck('product_item_id');

        $orderProducts = OrderProduct::where('order_id', $return->order_id)
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
            'customer' => $customerDetail,
            'items' => $items
        ];

        Mail::to($customer->email)->send(new ReturnConfirmation($returnData));

        return response()->json(['message' => 'Test return email sent successfully']);
    }
}
