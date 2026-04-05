1. Frontend Implementation (Livewire & Blade)

Main Entry Point:

- Livewire Class: app/Livewire/CheckoutPage.php
- Blade View: resources/views/livewire/checkout-page.blade.php
- Route: /checkout (Named: checkout.view)

Step-by-Step UI Flow:
The checkout is implemented as a single-page multi-step form controlled by the $currentStep integer property in the CheckoutPage component.

1. Shipping Address: Customer enters contact and delivery details.
2. Shipping Option: Selection of available shipping methods.
3. Billing Address: Billing details (can be toggled to match shipping).
4. Payment: Selection of payment method and final authorization.

Key Components & State Management:

- CheckoutPage (Orchestrator): Manages the overall $cart instance and $currentStep.
- Partials: UI steps are split into Blade partials for cleaner organization:
    - resources/views/partials/checkout/address.blade.php: Handles both Shipping and Billing.
    - resources/views/partials/checkout/shipping_option.blade.php: Lists methods from ShippingManifest.
    - resources/views/partials/checkout/payment.blade.php: Integrates Stripe or manual payment UI.
- State Persistence: Data is persisted to the database via the CartAddress model as the user completes each step, rather than storing everything in the session.
- Alpine.js Interactivity: Used for UI state like "Same as billing" toggles and loading states on buttons.

2. Backend Logic (Lunar Core Integration)

Livewire Lifecycle & Methods:

- `mount()`: Validates if a cart exists and determines the starting step based on existing cart data using determineCheckoutStep().
- `saveAddress(string $type)`: Validates input using getAddressValidation($type) and updates the cart's address via $this->cart->setShippingAddress() or $this->cart->setBillingAddress().
- `saveShippingOption()`: Uses CartSession::setShippingOption($option) to attach the selected method to the cart and recalculate totals.
- `checkout()`: The final submission method that triggers payment authorization.

Lunar Actions & Pipelines:

- Shipping Manifest: Lunar\Facades\ShippingManifest is used to fetch available shipping options.
- Payments Facade: Lunar\Facades\Payments handles the transition from Cart to Order during authorization.
- Recalculation: Every time an address or shipping option is saved, Lunar's internal pipelines automatically recalculate taxes and totals for the $cart object.

3. Data & Models

- `Lunar\Models\Cart`: The primary temporary record.
- `Lunar\Models\CartAddress`: Stores the shipping/billing data linked to the cart.
- `Lunar\Models\Order`: Created automatically by Lunar when a payment is authorized.
- Cart to Order Transition:
  When Payments::cart($cart)->authorize() is called, Lunar executes a pipeline that:
    1. Validates the cart state.
    2. Generates an Order record from the Cart data.
    3. Transfers addresses and lines to the new order.
    4. Links the order to the cart via completed_order_id.

4. Integrations & Payment Gateways

Stripe Integration:

- Component: <livewire:stripe.payment> (Provided by lunarphp/stripe package).
- Mechanism: Handles the Stripe Elements UI and communicates with Stripe to create a PaymentIntent.
- Webhooks: Handled by \Lunar\Stripe\Http\Controllers\WebhookController at the /stripe/webhook route.

Shipping Calculations:

- Table Rates: Uses lunarphp/table-rate-shipping.
- Custom Modifier: app/Modifiers/ShippingModifier.php can be used to inject manual shipping options (e.g., "Basic Delivery").

5. Post-Checkout Events

- Events: Lunar dispatches internal events like Lunar\Events\Orders\OrderCreated.
- Success Page: app/Livewire/CheckoutSuccessPage.php handles the final redirect, clears the CartSession, and displays the order reference.
- Background Jobs: The lunarphp/stripe package uses ProcessStripeWebhook to finalize orders if the frontend redirect fails.

Data Flow Summary

1. User Actions: User fills in addresses and selects shipping. Livewire updates the CartAddress records in real-time.
2. Payment Selection: User chooses "Card". The Stripe Livewire component initializes a Stripe Element.
3. Click "Pay": The Stripe component handles the client-side card validation and communicates with Stripe to authorize the amount.
4. Authorization: Upon successful authorization, the component (or the checkout() method) calls Lunar's Payments::authorize().
5. Finalization: Lunar's core logic freezes the cart, generates an Order, and creates a Transaction record.
6. Redirect: The user is redirected to /checkout/success, where the session cart is forgotten, and the order reference is displayed.
