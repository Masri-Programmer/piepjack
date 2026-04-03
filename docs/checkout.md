# Checkout Process Documentation

This document describes the checkout process for the Piepjack application.

## Overview

The checkout process handles user identification (or creation), address management, stock validation, and payment processing via Stripe.

## Automatic Discounts

### 5% Order Discount
An automatic **5% discount** is applied to the subtotal of any order that exceeds **100.00 EUR**.
- **Calculation:** `Discount = Subtotal * 0.05`
- **Implementation:** This is applied on both the frontend (for display) and backend (for final total and Stripe).
- **Stripe Integration:** The discount is passed to Stripe using a dynamic Coupon to ensure the correct amount is charged.

## Shipping Costs

- **Free Shipping:** Orders with a subtotal (after discount) of **70.00 EUR** or more qualify for free shipping.
- **Standard Shipping:** A flat fee of **5.90 EUR** is applied to orders below 70.00 EUR.
- **Pickup:** Using the promo code `pickup` sets the shipping cost to 0.00 EUR.

## Technical Workflow

1.  **User Management:** The system checks if a user with the provided email exists. If not, a new user is created.
2.  **Address Management:** The shipping address provided is saved or updated for the user.
3.  **Order Creation:** A pending order is created with a unique order number (format: `ORD-YYYYMM-XXXXX`).
4.  **Stock Validation:** Each product item's stock is checked. If insufficient, the checkout fails with an error.
5.  **Price Calculation:**
    -   Calculate Subtotal from line items.
    -   Apply 5% discount if Subtotal > 100 EUR.
    -   Calculate Shipping based on the new Subtotal.
    -   Update order's `total_price`.
6.  **Stripe Session:** A Stripe Checkout Session is created with line items and any applicable discounts.
7.  **Payment:** The user is redirected to Stripe to complete the payment.
8.  **Webhook:** Once payment is confirmed, the `checkout.session.completed` webhook updates the order status to `paid`, decrements stock, and sends a confirmation email.

## Local Development (Stripe Webhooks)

To test the checkout process locally and ensure orders are marked as `paid`, you must run the Stripe CLI to forward webhooks to your local server.

### Prerequisites

- [Stripe CLI](https://docs.stripe.com/stripe-cli) installed on your machine.

### Steps to Run Webhooks Locally

1.  **Login to Stripe:**
    ```bash
    stripe login
    ```

2.  **Start Forwarding Webhooks:**
    Forward events to your local API endpoint:
    ```bash
    stripe listen --forward-to localhost:8000/api/V1/shop/webhook/stripe
    ```
    *(Note: Adjust the port if your local server is running on a different port than 8000)*

3.  **Update Environment Variables:**
    The `stripe listen` command will generate a **Webhook signing secret** (starts with `whsec_`). Copy this value and update your `.env` file:
    ```env
    STRIPE_WEBHOOK_SECRET=whsec_your_secret_here
    ```

4.  **Keep the CLI Running:**
    The webhooks will only be processed as long as the `stripe listen` command is active.
