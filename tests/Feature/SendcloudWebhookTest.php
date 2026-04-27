<?php

use App\Mail\OrderDelivered;
use App\Mail\OrderShipped;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Order;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['services.sendcloud.secret_key' => 'test_secret']);

    // Setup Lunar requirements
    Currency::factory()->create([
        'code' => 'EUR',
        'default' => true,
    ]);
    Channel::factory()->create([
        'default' => true,
    ]);

    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('it verifies sendcloud signature', function () {
    $payload = [
        'action' => 'parcel_status_changed',
        'parcel' => [
            'tracking_number' => 'TEST12345',
            'status' => ['id' => 11, 'message' => 'In transit'],
        ],
    ];
    $jsonPayload = json_encode($payload);
    $invalidSignature = 'invalid';

    $response = $this->postJson('api/V1/shop/sendcloud/webhook', $payload, [
        'Sendcloud-Signature' => $invalidSignature,
    ]);

    $response->assertStatus(401);
});

test('it updates order status to shipped and sends email', function () {
    Mail::fake();

    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'payment-received',
        'tracking_number' => 'TRACK123',
    ]);

    $payload = [
        'action' => 'parcel_status_changed',
        'parcel' => [
            'tracking_number' => 'TRACK123',
            'status' => ['id' => 11, 'message' => 'In transit'],
        ],
    ];
    $jsonPayload = json_encode($payload);
    $signature = hash_hmac('sha256', $jsonPayload, 'test_secret');

    $response = $this->postJson('api/V1/shop/sendcloud/webhook', $payload, [
        'Sendcloud-Signature' => $signature,
    ]);

    $response->assertStatus(200);
    expect($order->fresh()->status)->toBe('shipped');

    Mail::assertSent(OrderShipped::class, function ($mail) use ($order) {
        return $mail->order->id === $order->id;
    });
});

test('it updates order status to delivered and sends email', function () {
    Mail::fake();

    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'shipped',
        'tracking_number' => 'TRACK123',
    ]);

    $payload = [
        'action' => 'parcel_status_changed',
        'parcel' => [
            'tracking_number' => 'TRACK123',
            'status' => ['id' => 14, 'message' => 'Delivered'],
        ],
    ];
    $jsonPayload = json_encode($payload);
    $signature = hash_hmac('sha256', $jsonPayload, 'test_secret');

    $response = $this->postJson('api/V1/shop/sendcloud/webhook', $payload, [
        'Sendcloud-Signature' => $signature,
    ]);

    $response->assertStatus(200);
    expect($order->fresh()->status)->toBe('delivered');

    Mail::assertSent(OrderDelivered::class, function ($mail) use ($order) {
        return $mail->order->id === $order->id;
    });
});
