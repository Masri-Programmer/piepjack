<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CheckoutRequest;
use App\Mail\AdminOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\User;
use App\Services\SendcloudService;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Order;
use Lunar\Models\ProductVariant;
use Stripe\Checkout\Session;
use Stripe\Coupon;
use Stripe\Customer;
use Stripe\PaymentIntent;
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
            'postal_code' => 'nullable|string|max:12',
            'products' => 'required|array',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required|integer|min:1',
            'email' => 'nullable|email',
        ]);

        // SILENT FIX: Only keep products that actually exist in the database
        // This prevents 422 errors from stale LocalStorage data
        $validProductIds = ProductVariant::whereIn('id', collect($request->products)->pluck('id'))->pluck('id')->toArray();
        $filteredProducts = collect($request->products)->filter(fn ($p) => in_array($p['id'], $validProductIds))->toArray();

        if (empty($filteredProducts)) {
            return response()->json(['data' => []]);
        }

        $user = $request->email ? User::where('email', $request->email)->first() : null;
        $cart = $this->getAndSyncCart($user, $filteredProducts);

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

        $order = Order::with('user')->where('cart_id', $cartId)->latest()->first();

        if (! $order) {
            return response()->json([
                'status' => 'pending',
                'message' => __('Order is being processed'),
            ]);
        }

        if ($order->user && $order->user->email !== $request->query('email')) {
            return response()->json(['message' => __('Unauthorized')], 403);
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

        try {
            // Use a lock to prevent duplicate orders from concurrent requests
            // We use the email as the lock key since it identifies the "identity" attempting checkout
            return Cache::lock('checkout_'.md5($validated['email']), 30)->block(10, function () use ($validated) {
                DB::beginTransaction();
                try {
                    $user = $this->findOrCreateUser($validated);

                    if (! $user->active) {
                        return response()->json(['message' => __('You are banned.')], 403);
                    }

                    $cart = $this->getAndSyncCart($user, $validated['products']);
                    $this->setCartAddresses($cart, $validated, $user);

                    // 1. UPDATE PROMO CODE FIRST
                    // This ensures the StoreShippingModifier sees the 'PICKUP' code
                    if (array_key_exists('promo_code', $validated)) {
                        $cart->update([
                            'coupon_code' => $validated['promo_code'] ? strtoupper($validated['promo_code']) : null,
                        ]);
                    }

                    // 2. FETCH SHIPPING OPTIONS AFTER PROMO CODE IS SET
                    $availableOptions = ShippingManifest::getOptions($cart);

                    // 3. AUTO-SELECT 'PICKUP' IF THE PROMO CODE IS ACTIVE
                    if (strtoupper($cart->coupon_code ?? '') === 'PICKUP') {
                        $shippingOption = $availableOptions->first(fn ($o) => $o->identifier === 'PICKUP');
                    } else {
                        $shippingOption = $availableOptions->first(fn ($o) => $o->identifier === $validated['shipping_method_id']);
                    }

                    if (! $shippingOption) {
                        throw new Exception(__('Shipping method unavailable. Please re-select.'));
                    }

                    // 4. APPLY THE ZERO-COST SHIPPING AND RECALCULATE
                    $cart->setShippingOption($shippingOption);
                    $cart->calculate();

                    $order = Order::where('cart_id', $cart->id)->latest()->first() ?: $cart->createOrder();
                    $customer = $order->user?->customers->first();

                    $order->update([
                        'status' => 'awaiting-payment',
                        'customer_id' => $customer?->id,
                        'placed_at' => $order->placed_at ?? now(),
                        'customer_reference' => config('shop.customer_reference_prefix').$order->user_id,
                    ]);

                    $session = $this->createStripeSession($cart, $user);

                    DB::commit();

                    return response()->json(['id' => $session->id, 'url' => $session->url]);
                } catch (Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            });
        } catch (LockTimeoutException $e) {
            return response()->json(['message' => __('Another checkout is in progress. Please wait.')], 409);
        } catch (Exception $e) {
            Log::error('Checkout error: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = StripeWebhook::constructEvent($payload, $sigHeader, config('services.stripe.webhook_secret'));
        } catch (Exception $e) {
            return response()->json(['message' => __('Invalid request')], 400);
        }

        $handledEvents = [
            'checkout.session.completed',
            'payment_intent.succeeded',
            'payment_intent.payment_failed',
            'payment_intent.canceled',
            'payment_intent.processing',
        ];

        if (! in_array($event->type, $handledEvents)) {
            return response()->json(['message' => __('Event ignored')]);
        }

        $stripeObject = $event->data->object;
        $cartId = $stripeObject->metadata->cart_id ?? null;
        $locale = $stripeObject->metadata->locale ?? config('app.locale');

        // Set app locale for this process (e.g. for the email)
        App::setLocale($locale);

        if (! $cartId || ! ($cart = Cart::find($cartId))) {
            return response()->json(['message' => __('Cart or Cart ID not found')], 404);
        }

        // 1. Determine the Charge ID (ch_...) - CRITICAL for refunds in Lunar
        $paymentIntentId = $stripeObject->payment_intent ?? ($stripeObject->object === 'payment_intent' ? $stripeObject->id : null);
        $chargeId = $stripeObject->charge ?? ($stripeObject->object === 'charge' ? $stripeObject->id : null);

        // If we only have a PI ID, try to get the latest charge ID from Stripe
        if (! $chargeId && $paymentIntentId) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $pi = PaymentIntent::retrieve($paymentIntentId);
                $chargeId = $pi->latest_charge;
            } catch (Exception $e) {
                Log::warning("Could not retrieve latest charge for PI {$paymentIntentId}: ".$e->getMessage());
            }
        }

        // Fallback to PI ID if charge ID still missing
        $referenceId = $chargeId ?? $paymentIntentId;

        // 2. Determine the status
        $stripeStatus = $stripeObject->status ?? 'succeeded';
        if ($event->type === 'checkout.session.completed') {
            if ($stripeObject->payment_status === 'paid') {
                $stripeStatus = 'succeeded';
            } elseif ($stripeObject->payment_status === 'unpaid') {
                $stripeStatus = 'requires_action'; // Maps to 'awaiting-payment' in Lunar
            } else {
                $stripeStatus = 'requires_payment_method';
            }
        }

        $lunarStatus = config("services.stripe.status_mapping.{$stripeStatus}") ?? 'processing';

        try {
            $lock = Cache::lock('stripe_webhook_cart_'.$cartId, 10);

            $lock->block(5, function () use ($cart, $lunarStatus, $referenceId) {
                $this->syncOrderStatus($cart, $lunarStatus, $referenceId);
            });

        } catch (LockTimeoutException $e) {
            Log::info('Webhook locked by another process for cart '.$cartId);

            return response()->json(['message' => __('Handled by another process')], 200);
        } catch (Exception $e) {
            Log::error('Webhook sync failed for cart '.$cartId.': '.$e->getMessage());

            return response()->json(['message' => __('Failed to sync order'), 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => __('Webhook handled successfully')]);
    }

    private function syncOrderStatus(Cart $cart, string $lunarStatus, ?string $referenceId): void
    {
        DB::beginTransaction();
        try {
            $order = Order::where('cart_id', $cart->id)->latest()->first() ?: $cart->createOrder();

            $customer = $order->user?->customers->first();

            $order->update([
                'status' => $lunarStatus,
                'customer_id' => $customer?->id,
                'placed_at' => $order->placed_at ?? now(),
                'customer_reference' => config('shop.customer_reference_prefix').$order->user_id,
            ]);

            // Only finalize shipping and emails if payment is fully received
            if ($lunarStatus === 'payment-received' && $referenceId && ! $order->transactions()->where('reference', $referenceId)->exists()) {
                $order->transactions()->create([
                    'success' => true,
                    'type' => 'capture',
                    'driver' => 'stripe',
                    'amount' => $order->total->value,
                    'reference' => $referenceId, // CRITICAL: Prefers ch_...
                    'status' => 'succeeded',
                    'card_type' => 'stripe',
                ]);

                // Decrement stock for each physical item in the order
                foreach ($order->lines as $line) {
                    if ($line->type === 'physical' && $line->purchasable instanceof ProductVariant) {
                        $line->purchasable->decrement('stock', $line->quantity);
                    }
                }

                $this->processShippingAndNotifications($order);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync order status: '.$e->getMessage());
            throw $e;
        }
    }

    private function findOrCreateUser(array $data): User
    {
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'password' => Hash::make(Str::random(24)),
                'active' => true,
            ]
        );

        if (! $user->wasRecentlyCreated) {
            $user->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
            ]);
        }

        // Ensure Lunar Customer exists
        if ($user->customers->isEmpty()) {
            $customer = LunarCustomer::create([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);

            $user->customers()->attach($customer);

            // Attach to default group
            $defaultGroup = CustomerGroup::whereDefault(true)->first();
            if ($defaultGroup) {
                $customer->customerGroups()->attach($defaultGroup);
            }
        }

        return $user;
    }

    private function getAndSyncCart(?User $user, array $products): Cart
    {
        $cart = CartSession::current();
        $customer = $user?->customers->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $user?->id,
                'customer_id' => $customer?->id,
                'currency_id' => Currency::getDefault()->id,
                'channel_id' => Channel::getDefault()->id,
            ]);
            CartSession::use($cart);
        } else {
            $cart->update([
                'user_id' => $user?->id,
                'customer_id' => $customer?->id,
            ]);
        }

        $cart->lines()->delete();
        foreach ($products as $product) {
            // Defensive: ensure we only add ProductVariant lines to the cart
            $cart->lines()->create([
                'purchasable_type' => ProductVariant::class,
                'purchasable_id' => $product['id'],
                'quantity' => (int) ($product['quantity'] ?? 1),
            ]);
        }

        return $cart->fresh();
    }

    private function setCartAddresses(Cart $cart, array $data, User $user): void
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

        // Save to Customer Address Book
        $customer = $user->customers()->first();
        if ($customer) {
            $customer->addresses()->updateOrCreate(
                [
                    'line_one' => $shippingData['line_one'] ?? '',
                    'postcode' => $shippingData['postcode'] ?? '',
                ],
                array_merge($shippingData, ['shipping_default' => true])
            );
        }

        if ($data['billing_same_as_shipping']) {
            $cart->setBillingAddress($shippingData);

            if ($customer) {
                $customer->addresses()->updateOrCreate(
                    [
                        'line_one' => $shippingData['line_one'] ?? '',
                        'postcode' => $shippingData['postcode'] ?? '',
                    ],
                    array_merge($shippingData, ['billing_default' => true])
                );
            }
        } else {
            $billingCountry = Country::where('iso2', $data['billing_country_code'])->first();
            $billingData = [
                'first_name' => $data['billing_first_name'],
                'last_name' => $data['billing_last_name'],
                'line_one' => $data['billing_street_address'],
                'city' => $data['billing_city'],
                'postcode' => $data['billing_postal_code'],
                'country_id' => $billingCountry?->id,
                'contact_email' => $data['email'],
            ];

            $cart->setBillingAddress($billingData);

            if ($customer) {
                $customer->addresses()->updateOrCreate(
                    [
                        'line_one' => $billingData['line_one'] ?? '',
                        'postcode' => $billingData['postcode'] ?? '',
                    ],
                    array_merge($billingData, ['billing_default' => true])
                );
            }
        }
    }

    private function createStripeSession(Cart $cart, User $user): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if (empty($user->stripe_id)) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name.' '.$user->last_name,
            ]);
            $user->update(['stripe_id' => $customer->id]);
        }

        $lineItems = collect($cart->lines)->map(fn ($line) => [
            'price_data' => [
                'currency' => strtolower($cart->currency->code),
                'product_data' => ['name' => $line->purchasable->product->translateAttribute('name')],
                'unit_amount' => (int) $line->unitPrice->value,
            ],
            'quantity' => $line->quantity,
        ])->toArray();

        if (($shipping = $cart->shippingTotal->value) > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($cart->currency->code),
                    'product_data' => ['name' => __('Shipping')],
                    'unit_amount' => (int) $shipping,
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
                'locale' => app()->getLocale(),
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'cart_id' => $cart->id,
                    'locale' => app()->getLocale(),
                ],
            ],
        ];

        if (($discount = $cart->discountTotal->value) > 0) {
            $coupon = Coupon::create([
                'amount_off' => (int) $discount,
                'currency' => strtolower($cart->currency->code),
                'duration' => 'once',
                'name' => __('Cart Discount'),
            ]);
            $sessionData['discounts'] = [['coupon' => $coupon->id]];
        }

        return Session::create($sessionData);
    }

    private function processShippingAndNotifications(Order $order): void
    {
        $order->load(['shippingAddress.country', 'user', 'lines', 'shippingLines']);

        // Safely load purchasable only for physical lines to avoid ArgumentCountError with ShippingOption
        $order->lines->where('type', 'physical')->load('purchasable.product');

        $shippingLine = $order->shippingLines->first();

        // Skip Sendcloud for Local Pickup orders
        if ($shippingLine && $shippingLine->identifier === 'PICKUP') {
            Log::info("Order #{$order->reference} is a pickup order. Skipping Sendcloud.");
            $this->sendOrderConfirmationEmail($order);

            return;
        }

        $customerData = [
            'name' => $order->user->first_name.' '.$order->user->last_name,
            'address' => $order->shippingAddress->line_one,
            'city' => $order->shippingAddress->city,
            'zip' => $order->shippingAddress->postcode,
            'country_code' => $order->shippingAddress->country->iso2 ?? config('shop.default_country'),
            'email' => $order->user->email,
            'order_number' => $order->reference,
        ];

        try {
            // Extract physical order lines and format for Sendcloud
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

            // Keep total parcel weight simple and hardcoded as requested
            $weight = 1.0;

            $shippingLine = $order->shippingLines->first();
            // Use the sendcloud_id from meta if available, otherwise default to configured method
            $shippingMethodId = config('services.sendcloud.default_method_id');
            if ($shippingLine && isset($shippingLine->meta['sendcloud_id'])) {
                $shippingMethodId = $shippingLine->meta['sendcloud_id'];
            }

            $shippingResult = $this->sendcloud->createParcel($customerData, $weight, (int) $shippingMethodId, false, $parcelItems);
            if ($shippingResult) {
                $currentMeta = (array) ($order->meta ?? []);
                $order->update([
                    'tracking_number' => $shippingResult['tracking_number'],
                    'label_url' => $shippingResult['label_url'],
                    'meta' => array_merge($currentMeta, [
                        'tracking_number' => $shippingResult['tracking_number'],
                        'label_url' => $shippingResult['label_url'],
                        'sendcloud_parcel_id' => $shippingResult['parcel_id'],
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
            'subtotal' => $order->sub_total instanceof Price ? $order->sub_total->decimal : (float) ($order->sub_total / 100),
            'discount' => $order->discount_total instanceof Price ? $order->discount_total->decimal : (float) ($order->discount_total / 100),
            'shipping' => $order->shipping_total instanceof Price ? $order->shipping_total->decimal : (float) ($order->shipping_total / 100),
            'total' => $order->total instanceof Price ? $order->total->decimal : (float) ($order->total / 100),
        ];

        Mail::to($order->user->email)->send(new OrderConfirmation($orderData));

        if (config('mail.from.address')) {
            Mail::to(config('mail.from.address'))->send(new AdminOrderNotification($orderData));
        }
    }
}
