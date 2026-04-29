<?php

namespace App\Modifiers;

use App\Services\SendcloudService;
use Closure;
use Illuminate\Support\Str;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\TaxClass;

class StoreShippingModifier extends ShippingModifier
{
    /**
     * Stripe-supported countries in Europe (ISO 3166-1 alpha-2)
     */
    protected const STRIPE_EUROPE_COUNTRIES = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE',
        'GR', 'HU', 'IE', 'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'NL', 'NO',
        'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'CH', 'GB', 'GI',
    ];

    /**
     * Allowed base shipping methods and their carriers.
     */
    protected const ALLOWED_METHODS = [
        'DHL Paket' => 'dhl_de',
        'DHL Paket International' => 'dhl_de',
        'DHL Paket Evening' => 'dhl_de',
        'DPD Classic' => 'dpd',
        'DPD Home' => 'dpd',
        'DPD Predict' => 'dpd',
        'DPD Business Express' => 'dpd',
    ];

    /**
     * Keywords that flag a method as too niche for standard checkout.
     */
    protected const NOISY_KEYWORDS = [
        'GoGreen', 'Alterssichtprüfung', 'Persönliche Übergabe',
        'Nachbarschaftszustellung', 'Filial Routing', 'Service point',
        'Shop2Home', 'KP',
    ];

    public function __construct(protected SendcloudService $sendcloud) {}

    public function handle(Cart $cart, Closure $next)
    {
        $countryCode = $cart->shippingAddress?->country?->iso2 ?? 'DE';

        // 1. Early Exit: Restrict to Stripe-supported European countries
        if (! in_array(strtoupper($countryCode), self::STRIPE_EUROPE_COUNTRIES, true)) {
            return $next($cart); // Skip adding shipping options
        }

        $taxClass = TaxClass::getDefault();

        // 2. Special Case: Pickup Promo Code
        if (strtoupper($cart->coupon_code ?? '') === 'PICKUP') {
            ShippingManifest::addOption(
                new ShippingOption(
                    name: __('shipping.pickup.name'),
                    description: __('shipping.pickup.description', ['address' => config('shop.address.full')]),
                    identifier: 'PICKUP',
                    price: new Price(0, $cart->currency, 1),
                    taxClass: $taxClass
                )
            );

            return $next($cart); // Skip Sendcloud methods if pickup is selected via promo code
        }

        $methods = $this->sendcloud->getShippingMethods();
        $seenBaseMethods = [];

        foreach ($methods as $method) {
            $name = $method['name'] ?? '';
            $carrier = $method['carrier'] ?? '';

            // 2. Validate Country Availability in Sendcloud
            $countryInfo = $this->getCountryInfo($method, $countryCode);
            if (! $countryInfo && ! empty($method['countries'])) {
                continue;
            }

            // 3. Skip Noisy Methods
            if (Str::contains($name, self::NOISY_KEYWORDS)) {
                continue;
            }

            // 4. Match and Deduplicate Base Methods
            $baseMatch = $this->getBaseMatch($name, $carrier);
            if (! $baseMatch || isset($seenBaseMethods[$baseMatch])) {
                continue;
            }

            // Mark as seen to prevent duplicate weight tiers
            $seenBaseMethods[$baseMatch] = true;

            // 5. Calculate Price (Fallback to 5.90€ if missing/zero)
            $priceValue = ($countryInfo && ($countryInfo['price'] ?? 0) > 0)
                ? (int) round($countryInfo['price'] * 100 + 2)
                : 590;

            // 6. Register Shipping Option
            ShippingManifest::addOption(
                new ShippingOption(
                    name: __($baseMatch),
                    description: __($method['service']['name'] ?? "Shipping via {$carrier}"),
                    identifier: 'SENDCLOUD_'.Str::upper(Str::slug($baseMatch, '_')),
                    price: new Price($priceValue, $cart->currency, 1),
                    taxClass: $taxClass,
                    meta: ['sendcloud_id' => $method['id'] ?? null]
                )
            );
        }

        return $next($cart);
    }

    /**
     * Retrieve pricing/country config for the target country code.
     */
    protected function getCountryInfo(array $method, string $countryCode): ?array
    {
        if (empty($method['countries'])) {
            return null;
        }

        return collect($method['countries'])->firstWhere('iso_2', $countryCode);
    }

    /**
     * Identify the base method if it matches our allowed list.
     */
    protected function getBaseMatch(string $name, string $carrier): ?string
    {
        foreach (self::ALLOWED_METHODS as $baseName => $allowedCarrier) {
            if (Str::contains($name, $baseName) && $carrier === $allowedCarrier) {
                return $baseName;
            }
        }

        return null;
    }
}
