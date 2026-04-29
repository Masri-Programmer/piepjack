<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Shop Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define default values for your shop, such as default
    | country codes, customer prefixes, and weight fallbacks.
    |
    */

    'default_country' => env('SHOP_DEFAULT_COUNTRY', 'DE'),

    'customer_reference_prefix' => env('SHOP_CUSTOMER_PREFIX', 'USER-'),

    'shipping' => [
        'default_weight' => (float) env('SHOP_SHIPPING_DEFAULT_WEIGHT', 0.5),
        'item_weight_fallback' => (float) env('SHOP_SHIPPING_ITEM_WEIGHT_FALLBACK', 0.2),
    ],

    'address' => [
        'owner' => 'Marvin Pieprzyk',
        'street' => 'Schollendamm 122a',
        'postcode' => '27751',
        'city' => 'Delmenhorst',
        'country' => 'Deutschland',
        'full' => 'Schollendamm 122a, 27751 Delmenhorst',
    ],

];
