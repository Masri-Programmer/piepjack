# Launch Registration & Notification Process

This document describes the process of capturing user registrations for the shop launch and the subsequent notification system when the shop goes live.

## Overview

The process consists of two main phases:
1. **User Registration:** Capturing the user's name and email during the countdown phase.
2. **Launch Notification:** Sending a "Shop is Live" email to all registered users once the countdown reaches zero.

---

## 1. User Registration

### Frontend Component
- **File:** `resources/js/pages/shop/LaunchCountdown.vue`
- **Logic:** 
  - Users provide their `name` and `email` in a form.
  - The form is validated locally (required fields and email format).
  - On submission, an AJAX POST request is sent to `/api/V1/shop/launch-registration`.

### Backend API
- **Route:** `POST /api/V1/shop/launch-registration` (defined in `routes/shop.php`)
- **Controller:** `App\Http\Controllers\API\V1\LaunchRegistrationController@store`
- **Process:**
  - Validates that the email is unique in the `launch_registrations` table.
  - Creates a new entry in the database.
  - Sends a confirmation email (`LaunchRegistrationMail`).
  - Returns a 201 JSON response on success.

### Database Table
- **Table Name:** `launch_registrations`
- **Fields:**
  - `id`: Auto-incrementing primary key.
  - `name`: User's name.
  - `email`: User's email address (unique).
  - `sent_online_notification_at`: Timestamp for when the "Shop is Live" notification was sent.
  - `created_at` / `updated_at`: Standard Laravel timestamps.

### Confirmation Email
- **Mailable:** `App\Mail\LaunchRegistrationMail`
- **Subject:** `Vielen Dank für deine Anmeldung! (Piepjack Drop)`
- **View:** `resources/views/emails/launchRegistration.blade.php`

---

## 2. Launch Notification

### Trigger Mechanisms
There are two ways the "Shop is Live" notification can be triggered:

#### A. Automatic Trigger (Frontend/API)
- **Frontend Logic:** In `LaunchCountdown.vue`, when the countdown reaches zero, it calls the API endpoint `POST /api/V1/shop/trigger-online-notification`.
- **Backend API:** `LaunchRegistrationController@triggerOnlineNotification`.
  - Verifies that the current time has actually passed the defined `VITE_LAUNCH_DATE`.
  - Checks for pending registrants (`sent_online_notification_at IS NULL`).
  - Internally triggers the Artisan command `shop:notify-online`.

#### B. Manual Trigger (Artisan Command)
- **Command:** `php artisan shop:notify-online`
- **Options:** `--force` (Resend emails even to those who already received one).
- **Class:** `App\Console\Commands\NotifyRegistrantsShopOnline`.
- **Process:**
  - Iterates through all registrants who haven't received a notification yet.
  - Sends the `ShopIsOnlineMail`.
  - Updates the `sent_online_notification_at` field for each successful send.

### Launch Notification Email
- **Mailable:** `App\Mail\ShopIsOnlineMail`
- **Subject:** `Der Shop ist jetzt LIVE! 🚀 (Piepjack Drop)`
- **View:** `resources/views/emails/shopIsOnline.blade.php`

---

## Important Configurations

- **Launch Date:** The target date is defined by the `VITE_LAUNCH_DATE` environment variable (defaulting to `2026-05-01T18:00:00`).
- **CSRF Protection:** The registration endpoint is excluded from CSRF validation in `bootstrap/app.php` to allow API-style requests from the frontend.
