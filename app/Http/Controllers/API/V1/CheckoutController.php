<?php

namespace App\Http\Controllers\API\V1;


use Exception;
use Stripe\Stripe;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\ProductItem;
use Illuminate\Support\Str;
use App\Models\OrderProduct;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;
use UnexpectedValueException;
use App\Mail\OrderConfirmation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Stripe\Webhook as StripeWebhook;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\API\V1\CheckoutRequest;
use Stripe\Exception\SignatureVerificationException;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Step 1: Find or create the user and their address
            $user = $this->findOrCreateUser($validated);
            if (!$user->active) {
                return response()->json(['message' => 'You are banned from using this website.'], 403);
            }
            $address = $this->createOrUpdateAddress($user, $validated);

            // Step 2: Create the order
            $order = $this->createOrder($user, $address);

            // Step 3: Process products and calculate initial total
            $lineItems = [];
            $totalPrice = 0;
            $this->processOrderProducts($order, $validated['products'], $lineItems, $totalPrice);

            // Step 4: Calculate shipping and add to total and line items
            $shippingCost = $this->calculateShippingCost($totalPrice, $validated);
            if ($shippingCost > 0) {
                $this->addShippingToLineItems($lineItems, $shippingCost);
            }
            $finalTotalPrice = $totalPrice + $shippingCost;
            $order->update(['total_price' => $finalTotalPrice]);

            // Step 5: Create Stripe Checkout Session
            $session = $this->createStripeSession($order, $user, $lineItems, $finalTotalPrice);

            DB::commit();

            return response()->json([
                'id' => $session->id,
                'url' => $session->url,
                'order_number' => $order->order_number,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Find an existing user or create a new one.
     */
    private function findOrCreateUser(array $data): User
    {
        return User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'password' => Hash::make(Str::random(24)),
                'active' => true,
            ]
        );
    }

    /**
     * Create or update the address for the user.
     */
    private function createOrUpdateAddress(User $user, array $data): Address
    {
        return Address::create([
            'user_id' => $user->id,
            'street_address' => $data['street_address'],
            'city' => $data['city'],
            'state_province' => $data['state_province'],
            'postal_code' => $data['postal_code'],
            'country_code' => $data['country_code'],
            'label' => 'Checkout Address',
        ]);
    }

    /**
     * Create the initial order record.
     */
    private function createOrder(User $user, Address $address): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'shipping_address_id' => $address->id,
            'billing_address_id' => $address->id,
            'status' => 'pending',
            'total_price' => 0,
        ]);
    }

    /**
     * Process products, check stock, create order items, and build Stripe line items.
     */
    private function processOrderProducts(Order $order, array $products, array &$lineItems, float &$totalPrice): void
    {
        $productIds = array_column($products, 'id');
        $productItems = ProductItem::with('product')->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($products as $product) {
            $productItem = $productItems->get($product['id']);

            if (!$productItem) {
                throw new Exception("Product with ID {$product['id']} not found.");
            }

            if (isset($productItem->stock) && $productItem->stock < $product['quantity']) {
                throw new Exception("Insufficient stock for {$productItem->product->name}. Available: {$productItem->stock}");
            }

            $pricePerItem = $productItem->price;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $productItem->product->name,
                        'images' => [$productItem->image ?? asset('images/logo_new_gray_bg_black.jpeg')],
                    ],
                    'unit_amount' => $pricePerItem * 100,
                ],
                'quantity' => $product['quantity'],
            ];

            OrderProduct::create([
                'order_id' => $order->id,
                'product_item_id' => $product['id'],
                'price_per_item' => $pricePerItem,
                'quantity' => $product['quantity'],
            ]);

            $totalPrice += ($pricePerItem * $product['quantity']);
        }
    }

    /**
     * Add the shipping cost as a line item for Stripe.
     */
    private function addShippingToLineItems(array &$lineItems, float $shippingCost): void
    {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Shipping Fee',
                ],
                'unit_amount' => $shippingCost * 100,
            ],
            'quantity' => 1,
        ];
    }

    /**
     * Create and return a Stripe Checkout Session.
     */
    private function createStripeSession(Order $order, User $user, array $lineItems, float $totalPrice): Session
    {


        return Session::create(
            [
                'payment_method_types' => config('services.stripe.payment_methods'),
                'mode' => 'payment',
                'line_items' => $lineItems,
                'success_url' => config('services.frontend_url') . '/success?order_number=' . $order->order_number,
                'cancel_url' => config('services.frontend_url') . '/checkout?order_number=' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'payment_type' => 'checkout',
                    'email' => $user->email,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ],
                ],
            ],
            ['idempotency_key' => $order->order_number . '-' . time()]
        );
    }

    private function calculateShippingCost($total_price, $validated)
    {
        $shipping_cost = $total_price >= 70 || $validated['promo_code'] === 'pickup' ? 0 : 5.90;

        return $shipping_cost;
    }

    /**
     * Handle Stripe webhook events
     */
    // cd "C:\Program Files\Stripe CLI\stripe_1.23.5_windows_x86_64"
    // stripe login
    // stripe listen --forward-to localhost:8000/api/V1/shop/webhook/stripe
    // $endpointSecret = 'whsec_1ec88b1f8be092cb44234aa740821ceb154cd06bdd5fe05b996b09ef79d33a94';
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = StripeWebhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (UnexpectedValueException | SignatureVerificationException $e) {
            Log::error('Stripe webhook error: Invalid signature or payload.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $session = $event->data->object;
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Webhook error: Order ID not found in metadata.', ['event' => $event->type]);
            return response()->json(['message' => 'Order ID not found'], 400);
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error('Webhook error: Order not found in database.', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                if ($order->status === 'pending') {
                    $this->handleSuccessfulPayment($order);
                }
                break;

            case 'checkout.session.expired':
                if ($order->status === 'pending') {
                    $order->update(['status' => 'canceled']);
                }
                break;
        }

        return response()->json(['message' => 'Webhook handled successfully']);
    }
    /**
     * Handle the logic for a successful payment.
     */
    private function handleSuccessfulPayment(Order $order): void
    {
        DB::beginTransaction();
        try {
            $order->update(['status' => 'paid']);

            // Decrement stock for each product
            foreach ($order->products as $orderProduct) {
                ProductItem::where('id', $orderProduct->product_item_id)
                    ->decrement('quantity', $orderProduct->quantity);
            }

            $this->sendOrderConfirmationEmail($order);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful payment.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
    /**
     * Send the order confirmation email.
     */
    private function sendOrderConfirmationEmail(Order $order): void
    {
        $order->load('user', 'shippingAddress', 'products.productItem.product', 'products.productItem.options.variation');

        $orderData = [
            'order' => $order,
            'user' => $order->user,
            'address' => $order->shippingAddress,
            'products' => $order->products->map(function ($orderProduct) {
                $productItem = $orderProduct->productItem;
                return [
                    'name' => $productItem->product->name,
                    'image' => $productItem->image ?? asset('images/logo_new_gray_bg_black.jpeg'),
                    'quantity' => $orderProduct->quantity,
                    'price_per_item' => $orderProduct->price_per_item,
                    'options' => $productItem->options->map(fn($option) => [
                        'name' => $option->variation->name,
                        'value' => $option->value,
                    ])->toArray(),
                ];
            }),
        ];

        Mail::to($order->user->email)->send(new OrderConfirmation($orderData));
    }

    /**
     * A public endpoint to manually trigger a test email for a given order.
     */
    public function sendTestEmail($orderId): JsonResponse
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        try {
            $this->sendOrderConfirmationEmail($order);
            return response()->json(['message' => 'Test email sent successfully']);
        } catch (Exception $e) {
            Log::error('Failed to send test email.', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to send test email.'], 500);
        }
    }
}
