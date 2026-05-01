<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\DiscountTypes\AmountOff;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Discount;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure we have a default currency and channel for Lunar
    if (! Currency::whereDefault(true)->exists()) {
        Currency::factory()->create(['default' => true, 'code' => 'EUR']);
    }
    if (! Channel::whereDefault(true)->exists()) {
        Channel::factory()->create(['default' => true, 'handle' => 'default']);
    }
    if (! CustomerGroup::whereDefault(true)->exists()) {
        CustomerGroup::factory()->create([
            'default' => true,
            'handle' => 'default',
            'name' => 'Default Group',
        ]);
    }
});

it('only returns active and usable discounts', function () {
    // 1. Create an active discount
    $activeDiscount = Discount::create([
        'name' => 'Active Discount',
        'handle' => 'active-discount',
        'type' => AmountOff::class,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'priority' => 1,
        'data' => [],
    ]);

    // 2. Create an expired discount
    $expiredDiscount = Discount::create([
        'name' => 'Expired Discount',
        'handle' => 'expired-discount',
        'type' => AmountOff::class,
        'starts_at' => now()->subDays(10),
        'ends_at' => now()->subDays(5),
        'priority' => 2,
        'data' => [],
    ]);

    // 3. Create a future discount
    $futureDiscount = Discount::create([
        'name' => 'Future Discount',
        'handle' => 'future-discount',
        'type' => AmountOff::class,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(10),
        'priority' => 3,
        'data' => [],
    ]);

    $response = getJson('/api/V1/shop/discounts');

    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Active Discount');
});
