<?php

namespace App\Modifiers;

use App\Services\SendcloudService;
use Closure;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\TaxClass;

class StoreShippingModifier extends ShippingModifier
{
    public function __construct(protected SendcloudService $sendcloud) {}

    public function handle(Cart $cart, Closure $next)
    {
        $taxClass = TaxClass::getDefault();
        $shippingAddress = $cart->shippingAddress;
        $countryCode = $shippingAddress?->country?->iso2 ?? 'DE';

        $methods = $this->sendcloud->getShippingMethods();

        $allowedMethods = [
            'DHL Paket' => ['carrier' => 'dhl_de'],
            'DPD Classic' => ['carrier' => 'dpd'],
            'DPD Business Express' => ['carrier' => 'dpd'],
            'DPD Parcelletter' => ['carrier' => 'dpd'],
            'Unstamped letter' => ['carrier' => 'sendcloud'],
        ];

        $seenBaseMethods = [];

        foreach ($methods as $method) {
            $name = $method['name'];
            $carrier = $method['carrier'];

            // Filter by country if countries are specified in the method
            if (! empty($method['countries'])) {
                $canShipTo = collect($method['countries'])->contains(fn ($c) => $c['iso_2'] === $countryCode);
                if (! $canShipTo) {
                    continue;
                }
            }

            // Simple filtering: Find if the name matches one of our allowed base names
            $baseMatch = null;
            foreach ($allowedMethods as $baseName => $config) {
                if (str_contains($name, $baseName) && $carrier === $config['carrier']) {
                    $baseMatch = $baseName;
                    break;
                }
            }

            if (! $baseMatch) {
                continue;
            }

            // Skip if we already added this base type (avoids weight tiers 0-2kg, 2-5kg etc.)
            if (isset($seenBaseMethods[$baseMatch])) {
                continue;
            }

            // Skip methods with specific suffixes that make them too niche for standard checkout
            $noisyKeywords = [
                'GoGreen', 'Alterssichtprüfung', 'Persönliche Übergabe',
                'Nachbarschaftszustellung', 'Filial Routing', 'Service point',
                'Shop2Home', 'KP',
            ];

            $isNoisy = false;
            foreach ($noisyKeywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    $isNoisy = true;
                    break;
                }
            }

            if ($isNoisy) {
                continue;
            }

            // Mark as seen so we don't add other weight variations of the same type
            $seenBaseMethods[$baseMatch] = true;

            $price = 590; // Default flat rate
            if (str_contains(strtolower($name), 'express')) {
                $price = 990;
            }

            // Clean up the name for display (e.g. "DHL Paket 0-2kg" -> "DHL Paket")
            $displayName = __($baseMatch);
            $displayDescription = __($method['service']['name'] ?? 'Shipping via '.$carrier);

            ShippingManifest::addOption(
                new ShippingOption(
                    name: $displayName,
                    description: $displayDescription,
                    identifier: 'sendcloud_'.$method['id'],
                    price: new Price($price, $cart->currency, 1),
                    taxClass: $taxClass
                )
            );
        }

        return $next($cart);
    }
}
