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
use Lunar\DataTypes\Price;
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

    public function getShippingMethods(Request $request): JsonResponse
    {
        $request->validate([
            'country_code' => 'required|string|max:2',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:lunar_product_variants,id',
            'products.*.quantity' => 'required|integer|min:1',
            'email' => 'nullable|email',
        ]);

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        }

        $cart = $this->getAndSyncCart($user, $request->products);

        $country = Country::where('iso2', $request->country_code)->first();
        $cart->setShippingAddress([
            'country_id' => $country?->id,
            'postcode' => $request->postal_code,
        ]);

        $options = ShippingManifest::getOptions($cart);

        return response()->json([
            'data' => $options->map(fn ($option) => [
                'id' => $option->identifier,
                'name' => $option->name,
                'description' => $option->description,
                'price' => $option->price->decimal,
                'formatted_price' => $option->price->formatted,
            ]),
        ]);
    }

    public function lookupOrder(Request $request, string $cartId): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        // FIX: Query Order directly by cart_id
        $order = Order::with('user')->where('cart_id', $cartId)->latest()->first();

        if (! $order) {
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

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user = $this->findOrCreateUser($validated);
            if (! $user->active) {
                return response()->json(['message' => 'You are banned from using this website.'], 403);
            }

            $cart = $this->getAndSyncCart($user, $validated['products']);
            $this->setCartAddresses($cart, $validated);

            $shippingHandle = $validated['shipping_method_id'];
            $availableOptions = ShippingManifest::getOptions($cart);
            $shippingOption = $availableOptions->first(fn ($option) => $option->identifier === $shippingHandle);

            if (! $shippingOption) {
                Log::warning('Shipping method mismatch during checkout', [
                    'requested' => $shippingHandle,
                    'available' => $availableOptions->pluck('identifier')->toArray(),
                    'cart_id' => $cart->id,
                    'address' => $validated['postal_code'],
                ]);

                throw new Exception('Selected shipping method is no longer available for this address or order total. Please re-select your shipping method.');
            }

            $cart->setShippingOption($shippingOption);

            if (! empty($validated['promo_code'])) {
                $cart->update([
                    'meta' => array_merge($cart->meta ?? [], ['promo_code' => $validated['promo_code']]),
                ]);
            }
            // 5. Calculate Cart (Lunar internal pipelines)
            $cart->calculate();

            $session = $this->createStripeSession($cart, $user, $validated['promo_code'] ?? null);

            DB::commit();

            return response()->json([
                'id' => $session->id,
                'url' => $session->url,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function getAndSyncCart(?User $user, array $products): Cart
    {
        $cart = CartSession::current();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $user?->id,
                'currency_id' => Currency::getDefault()->id,
                'channel_id' => Channel::getDefault()->id,
            ]);
            CartSession::use($cart);
        } else {
            $cart->update(['user_id' => $user?->id]);
        }

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

    private function createStripeSession(Cart $cart, User $user, ?string $promoCode = null): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if (empty($user->stripe_id)) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name.' '.$user->last_name,
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
                    'product_data' => ['name' => $product->translateAttribute('name')],
                    'unit_amount' => (int) ($line->unitPrice?->value ?? 0),
                ],
                'quantity' => $line->quantity,
            ];
        }

        $shippingTotal = $cart->shippingTotal?->value ?? 0;
        if ($shippingTotal > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($cart->currency->code),
                    'product_data' => ['name' => 'Shipping'],
                    'unit_amount' => (int) $shippingTotal,
                ],
                'quantity' => 1,
            ];
        }

        $sessionData = [
            'payment_method_types' => config('services.stripe.payment_methods'),
            'mode' => 'payment',
            'customer' => $user->stripe_id,
            'line_items' => $lineItems,
            'success_url' => config('services.frontend_url').'/success?cart_id='.$cart->id.'&email='.urlencode($user->email),
            'cancel_url' => config('services.frontend_url').'/checkout',
            'metadata' => [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
            ],
        ];

        // FIX 1: Removed manual double discount math! Only relying on Lunar pipeline.
        $discountTotal = $cart->discountTotal?->value ?? 0;
        // 5% discount for orders > 100 EUR
        // $subTotal = $cart->subTotal->value; // cents
        // $manualDiscount = 0;
        // if ($subTotal > 10000) {
        //     $manualDiscount += (int) ($subTotal * 0.05);
        // }

        // // Promo code "pickup" -> 10 EUR discount
        // if ($promoCode && strtolower(trim($promoCode)) === 'pickup') {
        //     $manualDiscount += 1000; // 10 EUR in cents
        // }

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
            [
                'password' => Hash::make(Str::random(24)),
                'active' => true,
            ]
        );

        // FIX 4: Always update name to match checkout
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);

        return $user;
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (! $sigHeader) {
            return response()->json(['message' => 'Missing Stripe-Signature header'], 400);
        }

        try {
            $event = StripeWebhook::constructEvent($payload, $sigHeader, config('services.stripe.webhook_secret'));
        } catch (Exception $e) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $handledEvents = ['checkout.session.completed', 'checkout.session.async_payment_succeeded'];
        if (! in_array($event->type, $handledEvents)) {
            return response()->json(['message' => 'Event ignored successfully']);
        }

        $session = $event->data->object;
        $cartId = $session->metadata->cart_id ?? null;

        if (! $cartId) {
            return response()->json(['message' => 'Cart ID not found'], 400);
        }

        $cart = Cart::find($cartId);

        if (! $cart) {
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

            $order->update([
                'status' => 'payment-received',
                'placed_at' => now(),
                'customer_reference' => 'USER-'.$order->user_id,
            ]);

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
            Log::error('Failed to finalize order: '.$e->getMessage());
        }
    }

    private function processShippingAndNotifications(Order $order): void
    {
        $order->load(['shippingAddress.country', 'user', 'lines']);

        $customerData = [
            'name' => $order->user->first_name.' '.$order->user->last_name,
            'address' => $order->shippingAddress->line_one,
            'city' => $order->shippingAddress->city,
            'zip' => $order->shippingAddress->postcode,
            'country_code' => $order->shippingAddress->country->iso2 ?? 'DE',
            'email' => $order->user->email,
        ];

        try {
            $shippingResult = $this->sendcloud->createParcel($customerData, 1.0, 8);
            if ($shippingResult) {
                // FIX 3: Safe ArrayObject casting
                $currentMeta = $order->meta ? $order->meta->toArray() : [];
                $order->update([
                    'meta' => array_merge($currentMeta, [
                        'tracking_number' => $shippingResult['tracking_number'],
                        'label_url' => $shippingResult['label_url'],
                    ]),
                ]);
            }
        } catch (Exception $e) {
            Log::warning('Shipping label generation failed: '.$e->getMessage());
        }

        $this->sendOrderConfirmationEmail($order);
    }

    private function sendOrderConfirmationEmail(Order $order): void
    {
        $productLines = $order->lines->filter(fn ($line) => $line->type === 'physical');

        $products = $productLines->map(function ($line) {
            $name = $line->description;
            if ($line->purchasable && $line->purchasable->product) {
                $name = $line->purchasable->product->translateAttribute('name');
            }

            return [
                'name' => $name,
                'quantity' => $line->quantity,
                'price_per_item' => $line->unit_price instanceof Price ? $line->unit_price->decimal : (float) ($line->unit_price / 100),
            ];
        });

        $orderData = [
            'order' => $order,
            'user' => $order->user,
            'address' => $order->shippingAddress,
            'products' => $products,
            // Using snake_case for Order columns
            'subtotal' => $order->sub_total instanceof Price ? $order->sub_total->decimal : (float) ($order->sub_total / 100),
            'discount' => $order->discount_total instanceof Price ? $order->discount_total->decimal : (float) ($order->discount_total / 100),
            'shipping' => $order->shipping_total instanceof Price ? $order->shipping_total->decimal : (float) ($order->shipping_total / 100),
            'total' => $order->total instanceof Price ? $order->total->decimal : (float) ($order->total / 100),
        ];

        Mail::to($order->user->email)->send(new OrderConfirmation($orderData));

        if (config('app.admin_email')) {
            Mail::to(config('app.admin_email'))->send(new AdminOrderNotification($orderData));
        }
    }
}
