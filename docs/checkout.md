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
