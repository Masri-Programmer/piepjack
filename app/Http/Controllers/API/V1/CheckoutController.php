<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CheckoutRequest;
use App\Mail\AdminOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\User;
use App\Services\SendcloudService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Order;
use Lunar\Models\ProductVariant;
use Stripe\Checkout\Session;
use Stripe\Coupon;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Webhook as StripeWebhook;

class CheckoutController extends Controller
{
    protected SendcloudService $sendcloud;

    public function __construct(SendcloudService $sendcloud)
    {
        $this->sendcloud = $sendcloud;
    }

    /**
     * Get available shipping methods for the current cart.
     */
    public function getShippingMethods(Request $request): JsonResponse
    {
        // 1. Validate basic info needed for shipping
        $request->validate([
            'country_code' => 'required|string|max:2',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:lunar_product_variants,id',
            'products.*.quantity' => 'required|integer|min:1',
            'email' => 'nullable|email',
        ]);

        // 2. Find or create a temporary user for the session if email is provided
        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        }

        // 3. Sync the cart from the request products
        $cart = $this->getAndSyncCart($user, $request->products);

        // 4. Update the shipping address on the cart to trigger correct zone matching
        $country = Country::where('iso2', $request->country_code)->first();
        $cart->setShippingAddress([
            'country_id' => $country?->id,
            'postcode' => $request->postal_code,
        ]);

        // 5. Fetch available options from Lunar's ShippingManifest
        $options = ShippingManifest::getOptions($cart);

        return response()->json([
            'data' => $options->map(fn($option) => [
                'id' => $option->identifier,
                'name' => $option->name,
                'description' => $option->description,
                'price' => $option->price->decimal,
                'formatted_price' => $option->price->formatted,
            ]),
        ]);
    }

    /**
     * Lookup an order by cart ID.
     */
    public function lookupOrder(Request $request, string $cartId): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        // NEW: Query the Order directly by cart_id instead of using completedOrder
        $order = Order::with('user')->where('cart_id', $cartId)->latest()->first();

        if (!$order) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Order is being processed',
            ]);
        }

        if ($order->user && $order->user->email !== $request->query('email')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $order->status,
            'reference' => $order->reference,
            'id' => $order->id,
        ]);
    }

    /**
     * Main checkout entry point.
     * Aligned with Lunar multi-step logic.
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. User Handling
            $user = $this->findOrCreateUser($validated);
            if (!$user->active) {
                return response()->json(['message' => 'You are banned from using this website.'], 403);
            }

            // 2. Cart Handling (Sync Vue state to Lunar Cart)
            $cart = $this->getAndSyncCart($user, $validated['products']);

            // 3. Address Handling
            $this->setCartAddresses($cart, $validated);

            // 4. Shipping Method Handling
            $shippingHandle = $validated['shipping_method_id'];
            $availableOptions = ShippingManifest::getOptions($cart);
            $shippingOption = $availableOptions->first(fn($option) => $option->identifier === $shippingHandle);

            if (!$shippingOption) {
                Log::warning('Shipping method mismatch during checkout', [
                    'requested' => $shippingHandle,
                    'available' => $availableOptions->pluck('identifier')->toArray(),
                    'cart_id' => $cart->id,
                    'address' => $validated['postal_code'],
                ]);

                throw new Exception('Selected shipping method is no longer available for this address or order total. Please re-select your shipping method.');
            }

            $cart->lines()->where('type', 'shipping')->delete();
            $cart->setShippingOption($shippingOption);

            if (!empty($validated['promo_code'])) {
                $cart->update([
                    'meta' => array_merge($cart->meta ?? [], ['promo_code' => $validated['promo_code']]),
                ]);
            }
            // 5. Calculate Cart (Lunar internal pipelines)
            $cart->calculate();

            // 6. Create Stripe Checkout Session
            $session = $this->createStripeSession($cart, $user, $validated['promo_code'] ?? null);

            DB::commit();

            return response()->json([
                'id' => $session->id,
                'url' => $session->url,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get or create a Lunar cart and sync items from request.
     */
    private function getAndSyncCart(?User $user, array $products): Cart
    {
        $cart = CartSession::current();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user?->id,
                'currency_id' => Currency::getDefault()->id,
                'channel_id' => Channel::getDefault()->id,
            ]);
            CartSession::use($cart);
        } else {
            $cart->update(['user_id' => $user?->id]);
        }

        // Sync lines
        $cart->lines()->delete();
        foreach ($products as $product) {
            $cart->lines()->create([
                'purchasable_type' => ProductVariant::class,
                'purchasable_id' => $product['id'],
                'quantity' => (int) ($product['quantity'] ?? 1),
            ]);
        }

        return $cart->fresh();
    }

    /**
     * Set shipping and billing addresses on the cart.
     */
    private function setCartAddresses(Cart $cart, array $data): void
    {
        $country = Country::where('iso2', $data['country_code'])->first();

        $shippingData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'line_one' => $data['street_address'],
            'city' => $data['city'],
            'state' => $data['state_province'],
            'postcode' => $data['postal_code'],
            'country_id' => $country?->id,
            'contact_email' => $data['email'],
        ];

        $cart->setShippingAddress($shippingData);

        if ($data['billing_same_as_shipping']) {
            $cart->setBillingAddress($shippingData);
        } else {
            $billingCountry = Country::where('iso2', $data['billing_country_code'])->first();
            $cart->setBillingAddress([
                'first_name' => $data['billing_first_name'],
                'last_name' => $data['billing_last_name'],
                'line_one' => $data['billing_street_address'],
                'city' => $data['billing_city'],
                'postcode' => $data['billing_postal_code'],
                'country_id' => $billingCountry?->id,
                'contact_email' => $data['email'],
            ]);
        }
    }

    /**
     * Create Stripe Checkout Session using Lunar Cart data.
     */
    private function createStripeSession(Cart $cart, User $user, ?string $promoCode = null): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if (empty($user->stripe_id)) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);
            $user->update(['stripe_id' => $customer->id]);
        }

        $lineItems = [];
        foreach ($cart->lines as $line) {
            $variant = $line->purchasable;
            $product = $variant->product;

            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($cart->currency->code),
                    'product_data' => [
                        'name' => $product->translateAttribute('name'),
                    ],
                    'unit_amount' => (int) ($line->unitPrice?->value ?? 0),
                ],
                'quantity' => $line->quantity,
            ];
        }

        // Add Shipping
        $shippingTotal = $cart->shippingTotal?->value ?? 0;
        if ($shippingTotal > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($cart->currency->code),
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => (int) $shippingTotal,
                ],
                'quantity' => 1,
            ];
        }

        // Add Discount as a negative line item if needed
        $sessionData = [
            'payment_method_types' => config('services.stripe.payment_methods'),
            'mode' => 'payment',
            'customer' => $user->stripe_id,
            'line_items' => $lineItems,
            'success_url' => config('services.frontend_url') . '/success?cart_id=' . $cart->id . '&email=' . urlencode($user->email),
            'cancel_url' => config('services.frontend_url') . '/checkout',
            'metadata' => [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
            ],
        ];

        // 5% discount for orders > 100 EUR
        $subTotal = $cart->subTotal->value; // cents
        $manualDiscount = 0;
        if ($subTotal > 10000) {
            $manualDiscount += (int) ($subTotal * 0.05);
        }

        // Promo code "pickup" -> 10 EUR discount
        if ($promoCode && strtolower(trim($promoCode)) === 'pickup') {
            $manualDiscount += 1000; // 10 EUR in cents
        }

        $discountTotal = $cart->discountTotal?->value ?? 0;

        if ($discountTotal > 0) {
            $coupon = Coupon::create([
                'amount_off' => (int) $discountTotal,
                'currency' => strtolower($cart->currency->code),
                'duration' => 'once',
                'name' => 'Cart Discount',
            ]);
            $sessionData['discounts'] = [['coupon' => $coupon->id]];
        }

        return Session::create($sessionData);
    }

    private function findOrCreateUser(array $data): User
    {
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            ['password' => Hash::make(Str::random(24)), 'active' => true]
        );

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);

        return $user;
    }

    /**
     * Handle Stripe Webhook to finalize Lunar Order.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        Log::info('Stripe webhook received', [
            'sig' => $sigHeader ? 'present' : 'missing',
        ]);

        if (!$sigHeader) {
            return response()->json(['message' => 'Missing Stripe-Signature header'], 400);
        }

        try {
            $event = StripeWebhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
            Log::info('Stripe event constructed', ['type' => $event->type]);
        } catch (Exception $e) {
            Log::error('Stripe webhook construction failed: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid request'], 400);
        }

        // 1. Define the only events we actually care about
        $handledEvents = [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded'
        ];

        // 2. If it's a payment intent, charge, or coupon event, ignore it and return 200 OK
        if (!in_array($event->type, $handledEvents)) {
            return response()->json(['message' => 'Event ignored successfully']);
        }

        // 3. Now we are safely guaranteed to be looking at a Checkout Session object
        $session = $event->data->object;
        $cartId = $session->metadata->cart_id ?? null;

        Log::info('Webhook processing', ['cart_id' => $cartId, 'event' => $event->type]);

        if (!$cartId) {
            return response()->json(['message' => 'Cart ID not found'], 400);
        }

        $cart = Cart::find($cartId);

        if (!$cart) {
            Log::warning('Cart not found for webhook', ['cart_id' => $cartId]);
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $this->handleSuccessfulPayment($cart);

        return response()->json(['message' => 'Webhook handled successfully']);
    }

    private function handleSuccessfulPayment(Cart $cart): void
    {
        DB::beginTransaction();
        try {
            $cart->calculate();
            $order = $cart->createOrder();
            Log::info('Lunar order created', ['order_id' => $order->id, 'cart_id' => $cart->id]);

            // Fix: Add Placed At and Kundenreferenz
            $order->update([
                'status' => 'payment-received',
                'placed_at' => now(),
                'customer_reference' => 'USER-' . $order->user_id, // Adds the reference!
            ]);

            // Fix: Create the Transaction so Lunar shows it as "Bezahlt"
            $order->transactions()->create([
                'success' => true,
                'type' => 'capture',
                'driver' => 'stripe',
                'amount' => $order->total->value,
                'reference' => 'Stripe Checkout',
                'status' => 'succeeded',
                'card_type' => 'stripe',
            ]);

            $this->processShippingAndNotifications($order);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to finalize order: ' . $e->getMessage());
        }
    }
    private function processShippingAndNotifications(Order $order): void
    {
        // Avoid deep eager load on lines to prevent MorphTo errors with non-Eloquent types like ShippingOption
        $order->load(['shippingAddress.country', 'user', 'lines']);

        // 1. Sendcloud integration
        $customerData = [
            'name' => $order->user->first_name . ' ' . $order->user->last_name,
            'address' => $order->shippingAddress->line_one,
            'city' => $order->shippingAddress->city,
            'zip' => $order->shippingAddress->postcode,
            'country_code' => $order->shippingAddress->country->iso2 ?? 'DE',
            'email' => $order->user->email,
        ];

        try {
            $shippingResult = $this->sendcloud->createParcel($customerData, 1.0, 8);
            if ($shippingResult) {
                $order->update([
                    'meta' => array_merge($order->meta ?? [], [
                        'tracking_number' => $shippingResult['tracking_number'],
                        'label_url' => $shippingResult['label_url'],
                    ]),
                ]);
            }
        } catch (Exception $e) {
            Log::warning('Shipping label generation failed: ' . $e->getMessage());
        }

        // 2. Email Notification
        $this->sendOrderConfirmationEmail($order);
    }

    private function sendOrderConfirmationEmail(Order $order): void
    {
        $productLines = $order->lines->filter(fn($line) => $line->type === 'physical');

        $products = $productLines->map(function ($line) {
            $name = $line->description;
            if ($line->purchasable && $line->purchasable->product) {
                $name = $line->purchasable->product->translateAttribute('name');
            }

            return [
                'name' => $name,
                'quantity' => $line->quantity,
                // MUST be unitPrice (camelCase)
                'price_per_item' => $line->unitPrice?->decimal ?? 0,
            ];
        });

        $orderData = [
            'order' => $order,
            'user' => $order->user,
            'address' => $order->shippingAddress,
            'products' => $products,
            // MUST be camelCase
            'subtotal' => $order->subTotal?->decimal ?? 0,
            'discount' => $order->discountTotal?->decimal ?? 0,
            'shipping' => $order->shippingTotal?->decimal ?? 0,
            'total' => $order->total?->decimal ?? 0,
        ];

        Mail::to($order->user->email)->send(new OrderConfirmation($orderData));

        if (config('app.admin_email')) {
            Mail::to(config('app.admin_email'))->send(new AdminOrderNotification($orderData));
        }
    }
}
