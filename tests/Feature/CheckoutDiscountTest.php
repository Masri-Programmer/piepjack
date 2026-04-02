<?php

use App\Models\User;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\postJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Checkout\Session;
use Stripe\Coupon;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock Stripe Session and Coupon static methods using alias:
    $sessionMock = Mockery::mock('alias:' . Session::class);
    // Since the controller expects Stripe\Checkout\Session type hint,
    // we need to make sure the mock is treated as such.
    // However, 'alias:' should technically work by replacing the class.
    
    // Let's create a real instance if possible or a more convincing mock.
    // Actually, the error says stdClass returned. This happens because 
    // we returned (object)[...]. Let's return the mock itself.
    
    $sessionMock->shouldReceive('create')
        ->andReturn($sessionMock);
    
    $sessionMock->id = 'sess_test';
    $sessionMock->url = 'https://checkout.stripe.com/test';

    $couponMock = Mockery::mock('alias:' . Coupon::class);
    $couponMock->shouldReceive('create')
        ->andReturn($couponMock);
    $couponMock->id = 'cpn_test';
});

afterEach(function () {
    Mockery::close();
});

it('applies a 5 percent discount on orders over 100 euros', function () {
    // 1. Create a product and product item
    $product = Product::factory()->create(['name' => 'Expensive Product']);
    $productItem = ProductItem::factory()->create([
        'product_id' => $product->id,
        'price' => 120.00, // Over 100
        'quantity' => 10,
    ]);

    $payload = [
        'email' => 'test@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'street_address' => '123 Main St',
        'city' => 'Berlin',
        'state_province' => 'Berlin',
        'postal_code' => '10115',
        'country_code' => 'DE',
        'products' => [
            ['id' => $productItem->id, 'quantity' => 1]
        ],
        'promo_code' => null,
    ];

    // Subtotal: 120.00
    // Discount (5%): 6.00
    // Shipping (Subtotal >= 70?): 0.00 (based on existing logic in CheckoutController)
    // Final Total: 114.00

    $response = postJson('/api/V1/shop/checkout', $payload);

    $response->assertSuccessful();
    
    $order = \App\Models\Order::latest()->first();
    // We expect 114.00
    expect((float)$order->total_price)->toBe(114.00);
});

it('does not apply a 5 percent discount on orders of 100 euros or less', function () {
    $product = Product::factory()->create(['name' => 'Cheap Product']);
    $productItem = ProductItem::factory()->create([
        'product_id' => $product->id,
        'price' => 50.00, // 100 or less
        'quantity' => 10,
    ]);

    $payload = [
        'email' => 'test@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'street_address' => '456 Side St',
        'city' => 'Hamburg',
        'state_province' => 'Hamburg',
        'postal_code' => '20095',
        'country_code' => 'DE',
        'products' => [
            ['id' => $productItem->id, 'quantity' => 1]
        ],
        'promo_code' => null,
    ];

    // Subtotal: 50.00
    // Discount: 0.00
    // Shipping (Subtotal < 70): 5.90
    // Final Total: 55.90

    $response = postJson('/api/V1/shop/checkout', $payload);

    $response->assertSuccessful();

    $order = \App\Models\Order::latest()->first();
    expect((float)$order->total_price)->toBe(55.90);
});
