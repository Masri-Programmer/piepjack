<?php

namespace App\Http\Controllers\API\V1;

use Stripe\Stripe;
use App\Models\Order;
use App\Models\Customer;
use App\Models\ProductItem;
use App\Models\OrderProduct;
use Stripe\Checkout\Session;
use UnexpectedValueException;
use App\Models\CustomerDetail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\API\V1\CheckoutRequest;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user = Customer::where('email', $request->email)->first();

            if ($user && !$user->active) {
                return response(['message' => 'You are banned from using this website.'], 403);
            }

            if (!$user) {
                $user = Customer::create(['email' => $request->email]);
            }

            $customer_id = $user->id;

            $customer_details = CustomerDetail::where('customer_id', $customer_id)->first();

            if (!$customer_details) {
                $customer_details = CustomerDetail::create([
                    'customer_id' => $customer_id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'address_line_one' => $validated['address_line_one'],
                    'address_line_two' => $validated['address_line_two'],
                ]);
            } else {
                $customer_details->update([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'address_line_one' => $validated['address_line_one'],
                    'address_line_two' => $validated['address_line_two'],
                ]);
            }


            $order = Order::create([
                'status' => 'pending',
                'customer_details_id' => $customer_details->id,
                'total_price' => 0,
            ]);

            $products = $validated['products'];
            $line_items = [];
            $total_price = 0;

            $product_ids = array_column($products, 'id');
            $product_items = ProductItem::with(['product', 'options.variation'])
                ->whereIn('id', $product_ids)
                ->get()
                ->keyBy('id');

            foreach ($products as $product) {
                $product_id = $product['id'];

                if (!isset($product_items[$product_id])) {
                    throw new \Exception("Product with ID {$product_id} not found");
                }

                $_product = $product_items[$product_id];

                if (isset($_product->stock) && $_product->stock < $product['quantity']) {
                    DB::rollBack();
                    return response([
                        'message' => "Insufficient stock for {$_product->product->name}. Available: {$_product->stock}",
                        'product_id' => $product_id
                    ], 400);
                }

                $price_per_item = $_product->price;
                $name = $_product->product->name;
                $product_image = $_product->image ?? asset('images/logo_new_gray_bg_black.jpeg');

                $options = $_product->options->map(function ($option) {
                    return $option->variation->name . ': ' . $option->value;
                })->toArray();

                $description = !empty($options) ? implode(', ', $options) : 'No options selected';

                array_push($line_items, [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $name,
                            'images' => [$product_image],
                            'description' => $description,
                        ],
                        'unit_amount' => $price_per_item * 100,
                    ],
                    'quantity' => $product['quantity'],
                ]);

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_item_id' => $product_id,
                    'price_per_item' => $price_per_item,
                    'quantity' => $product['quantity'],
                ]);


                $total_price += ($price_per_item * $product['quantity']);
            }

            $shipping_cost = $this->calculateShippingCost($total_price, $validated);
            $total_price += $shipping_cost;

            $order->update(['total_price' => $total_price]);

            if ($shipping_cost > 0) {
                array_push($line_items, [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Shipping Fee',
                            'description' => 'Standard Shipping',
                        ],
                        'unit_amount' => $shipping_cost * 100,
                    ],
                    'quantity' => 1,
                ]);
            }

            $success_url = env('FRONTEND_URL') . '/success?order_number=' . $order->order_number;
            $cancel_url = env('FRONTEND_URL') . '/checkout?order_number=' . $order->order_number;

            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            try {
                $session = Session::create(
                    [
                        'mode' => 'payment',
                        'line_items' => $line_items,
                        'metadata' => [
                            'order_number' => $order->order_number,
                            'order_id' => $order->id,
                            'payment_type' => 'checkout',
                            'email' => $user->email,
                            'total_price' => $total_price,
                        ],
                        'success_url' => $success_url,
                        'cancel_url' => $cancel_url,
                        'payment_intent_data' => [
                            'metadata' => [
                                'order_id' => $order->id,
                                'order_number' => $order->order_number,
                            ],
                        ],
                    ],
                    ['idempotency_key' => $order->order_number . '-' . time()]
                );

                DB::commit();

                return response([
                    'id' => $session->id,
                    'url' => $session->url,
                    'order_number' => $order->order_number,
                ], 200);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                DB::rollBack();
                Log::error('Stripe API Error: ' . $e->getMessage(), [
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
                return response(['message' => 'Payment processing error: ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response(['message' => 'An error occurred during checkout. Please try again.'], 500);
        }
    }

    private function calculateShippingCost($total_price, $validated)
    {
        $shipping_cost = $total_price >= 100 || $validated['promo_code'] === 'pickup' ? 0 : 5.90;

        return $shipping_cost;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook()
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        // $endpoint_secret = 'whsec_1ec88b1f8be092cb44234aa740821ceb154cd06bdd5fe05b996b09ef79d33a94';
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            Log::error('Webhook error: Invalid payload', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook error: Invalid signature', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $session = $event->data->object;
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Webhook error: Order ID not found in metadata', [
                'event' => $event->type,
                'metadata' => $session->metadata ?? 'No metadata',
            ]);
            return response()->json(['message' => 'Order ID not found in metadata'], 400);
        }

        try {
            $order = Order::findOrFail($orderId);

            // Check for valid order status transition
            $validTransitions = [
                'pending' => ['paid', 'canceled'],
                'processing' => ['paid', 'canceled'],
                'canceled' => ['pending', 'paid'],
                'paid' => []
            ];

            switch ($event->type) {
                case 'checkout.session.async_payment_failed':
                case 'checkout.session.expired':
                    if (in_array('canceled', $validTransitions[$order->status] ?? [])) {
                        $order->update(['status' => 'canceled']);
                    }
                    break;

                case 'checkout.session.completed':
                    if (in_array('paid', $validTransitions[$order->status] ?? [])) {
                        DB::beginTransaction();
                        try {
                            $order->update([
                                'status' => 'paid',
                                // 'payment_id' => $session->payment_intent ?? null,
                                // 'paid_at' => now(),
                            ]);

                            // Update product stock
                            $orderProducts = OrderProduct::where('order_id', $order->id)->get();

                            // foreach ($orderProducts as $orderProduct) {
                            //     $productItem = ProductItem::find($orderProduct->product_item_id);

                            //     if ($productItem) {
                            //         // Decrement the quantity
                            //         $newQuantity = max(0, $productItem->quantity - $orderProduct->quantity);
                            //         $productItem->update(['quantity' => $newQuantity]);

                            //         // If stock is depleted, potentially mark as inactive
                            //         if ($newQuantity === 0) {
                            //             $productItem->update(['active' => false]);
                            //             Log::info("Product item ID {$productItem->id} marked as inactive due to zero stock");
                            //         }
                            //     }
                            // }

                            $customerDetail = CustomerDetail::find($order->customer_details_id);
                            $customer = $customerDetail ? Customer::find($customerDetail->customer_id) : null;

                            if ($customer) {
                                if ($orderProducts->isEmpty()) {
                                    Log::error('Order has no products', ['order_id' => $order->id]);
                                }

                                $orderItems = ProductItem::with(['product', 'options.variation'])
                                    ->whereIn('id', $orderProducts->pluck('product_item_id'))
                                    ->get();

                                $orderData = [
                                    'order' => $order->toArray(),
                                    'customer' => $customerDetail->toArray(),
                                    'products' => $orderProducts->map(fn($orderProduct) => [
                                        'quantity' => $orderProduct->quantity,
                                        'price_per_item' => $orderProduct->price_per_item,
                                    ]),
                                    'items' => $orderItems->map(fn($item) => [
                                        'name' => $item->product->name,
                                        'image' => $item->image ?? asset('images/logo_new_gray_bg_black.jpeg'),
                                        'price' => $item->price,
                                        'options' => $item->options->map(fn($option) => [
                                            'name' => $option->variation->name,
                                            'value' => $option->value,
                                        ])->toArray(),
                                    ]),
                                ];

                                Mail::to($customer->email)->send(new OrderConfirmation($orderData));
                            }

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('Error processing paid order', [
                                'order_id' => $order->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            throw $e;
                        }
                    } else {
                        Log::warning("Invalid order status transition attempted", [
                            'order_id' => $order->id,
                            'current_status' => $order->status,
                            'attempted_status' => 'paid',
                        ]);
                    }
                    break;

                case 'payment_intent.payment_failed':
                    if (in_array('canceled', $validTransitions[$order->status] ?? [])) {
                        $order->update(['status' => 'canceled']);
                    }
                    break;

                case 'payment_intent.succeeded':
                    // This is a backup in case the checkout.session.completed event doesn't trigger
                    if ($order->status !== 'paid' && in_array('paid', $validTransitions[$order->status] ?? [])) {
                        DB::beginTransaction();
                        try {
                            $order->update(['status' => 'paid', 'paid_at' => now()]);

                            // Update product stock here too as a backup
                            $orderProducts = OrderProduct::where('order_id', $order->id)->get();

                            foreach ($orderProducts as $orderProduct) {
                                $productItem = ProductItem::find($orderProduct->product_item_id);

                                if ($productItem) {
                                    $newQuantity = max(0, $productItem->quantity - $orderProduct->quantity);
                                    $productItem->update(['quantity' => $newQuantity]);

                                    if ($newQuantity === 0) {
                                        $productItem->update(['active' => false]);
                                    }
                                }
                            }

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('Error updating stock on payment_intent.succeeded', [
                                'order_id' => $order->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    break;

                default:
                    return response()->json(['message' => 'Unhandled event type: ' . $event->type], 200);
            }

            return response()->json(['message' => 'Webhook handled successfully']);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'event' => $event->type,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Error processing webhook: ' . $e->getMessage()], 500);
        }
    }

    public function sendTestEmail()
    {
        $order = Order::find(15); // You can adjust this based on a real test order ID
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Simulating customer and product details for the test
        $customerDetail = CustomerDetail::find($order->customer_details_id);
        $customer = $customerDetail ? Customer::find($customerDetail->customer_id) : null;

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $orderProducts = OrderProduct::where('order_id', $order->id)->get();
        $orderItems = ProductItem::with(['product', 'options.variation'])
            ->whereIn('id', $orderProducts->pluck('product_item_id'))
            ->get();

        $orderData = [
            'order' => $order->toArray(),
            'customer' => $customerDetail->toArray(),
            'products' => $orderProducts->map(fn($orderProduct) => [
                'quantity' => $orderProduct->quantity,
                'price_per_item' => $orderProduct->price_per_item,
            ]),
            'items' => $orderItems->map(fn($item) => [
                'name' => $item->product->name,
                'image' => $item->image ?? asset('images/logo_new_gray_bg_black.jpeg'),
                'price' => $item->price,
                'options' => $item->options->map(fn($option) => [
                    'name' => $option->variation->name,
                    'value' => $option->value,
                ])->toArray(),
            ]),
        ];

        Mail::to($customer->email)->send(new OrderConfirmation($orderData));

        return response()->json(['message' => 'Test email sent successfully']);
    }
}
