<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Lunar\Models\Language;
use Lunar\Models\Currency;
use Lunar\Models\Country;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\ProductType;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Lunar\Models\CustomerGroup;
use Lunar\Models\ProductOption;
use Lunar\Shipping\Models\ShippingZone;
use Lunar\Shipping\Models\ShippingMethod;
use Lunar\FieldTypes\TranslatedText;
use Lunar\FieldTypes\Text;

class LunarStoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Languages
        Language::updateOrCreate(['code' => 'de'], [
            'name' => 'German',
            'default' => true,
        ]);
        Language::updateOrCreate(['code' => 'en'], [
            'name' => 'English',
            'default' => false,
        ]);

        // 2. Default Currency (EUR)
        Currency::updateOrCreate(['code' => 'EUR'], [
            'name' => 'Euro',
            'decimal_places' => 2,
            'default' => true,
            'enabled' => true,
            'exchange_rate' => 1,
        ]);

        // 3. Germany VAT (19%)
        $taxClass = TaxClass::firstOrCreate(['name' => 'Default Tax Class'], ['default' => true]);

        $country = Country::where('iso2', 'DE')->first();
        if (!$country) {
            $country = Country::create([
                'name' => 'Germany',
                'iso2' => 'DE',
                'iso3' => 'DEU',
                'phonecode' => '49',
                'capital' => 'Berlin',
                'currency' => 'EUR',
                'native' => 'Deutschland',
                'emoji' => '🇩🇪',
                'emoji_u' => 'U+1F1E9 U+1F1EA',
            ]);
        }

        $taxZone = TaxZone::updateOrCreate(['name' => 'Germany'], [
            'zone_type' => 'country',
            'price_display' => 'tax_inclusive',
            'active' => true,
            'default' => true,
        ]);

        $taxZone->countries()->firstOrCreate([
            'country_id' => $country->id,
        ]);

        $taxRate = TaxRate::updateOrCreate([
            'tax_zone_id' => $taxZone->id,
            'name' => 'Standard VAT',
        ], [
            'priority' => 1,
        ]);

        TaxRateAmount::updateOrCreate([
            'tax_class_id' => $taxClass->id,
            'tax_rate_id' => $taxRate->id,
        ], [
            'percentage' => 19.0,
        ]);

        // 4. Collections
        $collectionGroup = CollectionGroup::firstOrCreate([
            'handle' => 'main',
        ], [
            'name' => 'Main',
        ]);

        $collectionNames = ['T-shirts', 'Sweaters', 'Jackets', 'Sports', 'Accessories', 'Underwear'];
        foreach ($collectionNames as $name) {
            $collection = Collection::where('collection_group_id', $collectionGroup->id)->get()->first(function ($c) use ($name) {
                return $c->translateAttribute('name') === $name;
            });

            if ($collection) {
                $collection->update([
                    'attribute_data' => [
                        'name' => new TranslatedText(collect(['de' => $name, 'en' => $name])),
                    ],
                ]);
            } else {
                Collection::create([
                    'collection_group_id' => $collectionGroup->id,
                    'type' => 'static',
                    'attribute_data' => [
                        'name' => new TranslatedText(collect(['de' => $name, 'en' => $name])),
                    ],
                ]);
            }
        }

        // 5. Variants / Options (Size, Color, Material)
        $options = ['Size', 'Color', 'Material'];
        foreach ($options as $opt) {
            ProductOption::updateOrCreate([
                'handle' => Str::slug($opt),
            ], [
                'name' => ['de' => $opt, 'en' => $opt],
                'label' => ['de' => $opt, 'en' => $opt],
            ]);
        }

        // 6. Shipping Methods (Requires lunarphp/table-rate-shipping)
        $shippingZone = ShippingZone::firstOrCreate(['name' => 'Germany'], ['type' => 'unrestricted']);

        ShippingMethod::updateOrCreate(['code' => 'standard'], [
            'name' => 'Standard Shipping',
            'driver' => 'ship-by',
            'enabled' => true,
        ]);

        ShippingMethod::updateOrCreate(['code' => 'express'], [
            'name' => 'Express Shipping',
            'driver' => 'ship-by',
            'enabled' => true,
        ]);

        // 7. Products & Prices
        $productType = ProductType::firstOrCreate(['name' => 'Default']);
        $customerGroup = CustomerGroup::where('default', true)->first() ?: CustomerGroup::create(['name' => 'General', 'handle' => 'general', 'default' => true]);
        $currency = Currency::where('code', 'EUR')->firstOrFail();

        $this->createProduct(
            'DREAM ZIP HOODIE',
            $this->dreamHoodieDescription(),
            7495,
            7495,
            6995,
            $productType,
            $taxClass,
            $customerGroup,
            $currency
        );

        $this->createProduct(
            'VANILLA SOFT JACKET',
            $this->vanillaJacketDescription(),
            6671,
            6671,
            null,
            $productType,
            $taxClass,
            $customerGroup,
            $currency
        );

        $this->createProduct(
            'LEGACY T-SHIRT',
            $this->legacyTshirtDescription(),
            6671,
            6671,
            null,
            $productType,
            $taxClass,
            $customerGroup,
            $currency
        );
    }

    private function createProduct(
        string $name,
        string $description,
        int $basePrice,
        int $comparePrice,
        ?int $discountPrice,
        $productType,
        $taxClass,
        $customerGroup,
        $currency
    ): void {
        $product = Product::with('variants')->get()->first(function ($p) use ($name) {
            return $p->translateAttribute('name') === $name;
        });

        if ($product) {
            $product->update([
                'attribute_data' => [
                    'name' => new TranslatedText(collect(['de' => $name, 'en' => $name])),
                    'description' => new TranslatedText(collect(['de' => $description, 'en' => $description])),
                ],
                'product_type_id' => $productType->id,
            ]);
        } else {
            $product = Product::create([
                'status' => 'published',
                'product_type_id' => $productType->id,
                'brand_id' => null,
                'attribute_data' => [
                    'name' => new TranslatedText(collect(['de' => $name, 'en' => $name])),
                    'description' => new TranslatedText(collect(['de' => $description, 'en' => $description])),
                ],
            ]);
        }

        // Create base variant
        $variant = ProductVariant::updateOrCreate([
            'product_id' => $product->id,
            'sku' => Str::slug($name),
        ], [
            'tax_class_id' => $taxClass->id,
            'stock' => 100,
            'shippable' => true,
            'unit_quantity' => 1,
        ]);

        // Prices in Lunar are stored in CENTS
        Price::updateOrCreate([
            'priceable_id' => $variant->id,
            'priceable_type' => ProductVariant::class,
            'currency_id' => $currency->id,
            'customer_group_id' => $customerGroup->id,
            'min_quantity' => 1,
        ], [
            'price' => $discountPrice ?: $basePrice,
            'compare_price' => $discountPrice ? $comparePrice : null,
        ]);
    }

    private function dreamHoodieDescription(): string
    {
        return '<div><p><em>Der DREAM ZIP HOODIE steht für deine Ziele und Träume, die du eines Tages zur Realität machst. Lockerer Fit, maximaler Komfort und edle Details vereinen sich mit einem hochwertigen Off-White Puff Print sowie erstklassigem Material, für ein luxuriöses und angenehmes Tragegefühl.</em></p><p></p><p></p><p><span style="color: rgb(255, 251, 185)"><strong><em>DETAILS</em></strong></span></p><p></p><p></p><p><em>- 70% Baumwolle, 30% Polyester</em></p><p><em>- 330 g/m Hochwertiger, schwerer Stoff</em></p><p><em>- Brushed Fleece (angeraut innen)</em></p><p><em>- Oversize Fit</em></p><p><em>- Unisex</em></p><p><em>- Made in Turkey</em></p><p></p><p></p><p><span style="color: rgb(255, 251, 185)"><strong><em>PASSFORM</em></strong></span></p><p></p><p><em>Das Model ist ca. xxxxxm groß und trägt Größe x.</em></p><p></p><p></p><p><span style="color: rgb(255, 251, 185)"><strong><em>PFLEGEHINWEISE</em></strong></span></p><p><em>- Waschbar bei 30°C</em></p><p><em>- Auf links waschen</em></p><p><em>- nicht bleichen</em></p><p><em>- Nicht in den Trockner</em></p><p><em>- Auf links bügeln</em></p><p><em>- Nicht chemisch reinigen</em></p></div>';
    }

    private function vanillaJacketDescription(): string
    {
        return '<div><p><em>Die VANILLA SOFT JACKE überzeugt durch ihr außergewöhnlich weiches Material, den cleanen Ton-in-Ton Look in (Beige) und ihre hochwertigen Details.</em></p><p><em>Dezent, edel und gleichzeitig auffällig auf ihre eigene Art.</em></p><p></p><p><em>Maximaler Komfort trifft auf einen luxuriösen Look, perfekt für jede Jahreszeit, von kühlen Sommernächten bis in die kalte Saison.</em></p><p></p><p></p><p></p><p></p><p><span style="color: rgb(255, 249, 149)"><strong>DETAILS</strong></span></p><p></p><p><em>-100% Polyester Wellsoft</em></p><ul><li><p>- 290 g/m <em>Hochwertiger, weicher Stoff</em></p></li><li><p><em>- Dezente PIEPJACK Branding - Elemente</em></p><ul><li><p><em>- Lockerer Fit</em></p></li><li><p><em>- Unisex</em></p></li><li><p><em>- Made in Turkey</em></p></li></ul></li></ul><p></p><p></p><p></p><p><span style="color: #fffbb9"><strong>PASSFORM</strong></span></p><p></p><p><em>Das Model ist ca. 1,79 cm groß und trägt Größe L</em></p><p></p><p></p><p></p><p><span style="color: rgb(255, 249, 149)"><strong>PFLEGEHINWEISE</strong></span></p><p></p><p><em>- Waschbar bei 30°C</em></p><ul><li><p>- Auf links waschen</p></li><li><p>- Nicht bleichen</p></li><li><p>- Nicht in den Trockner</p></li><li><p>- Auf links bügeln</p></li><li><p>- Nicht chemisch reinigen</p></li><li><p></p></li></ul></div>';
    }

    private function legacyTshirtDescription(): string
    {
        return '<div><p><em>Das LEGACY T-SHIRT überzeugt durch ein cleanes Design mit minimalistischem Frontprint und auffälligem Backprint. Ein moderner, lockerer Schnitt trifft auf einen besonders weichen, hochwertigen Stoff, der sich angenehm auf der Haut anfühlt, für maximalen Komfort im Alltag.</em></p><p></p><p><span style="color: #fffbb9"><strong>DETAILS</strong></span></p><p></p><ul><li><p><em>- 100% Premium Baumwolle</em></p></li><li><p><em>- Dichtgewebte Heavyweight Baumwolle - 230g/m²</em></p></li><li><p><em>- Oversize Fit</em></p></li><li><p><em>- Gut anliegender Kragen</em></p></li><li><p><em>- Unisex</em></p><p><em>- Made in Turkey</em></p></li></ul><p></p><p><span style="color: #fffbb9"><strong>PASSFORM</strong></span></p><p></p><p><em>Das Model ist ca. 1,90 m groß und trägt Größe L.</em></p><p></p><p><span style="color: #fffbb9"><strong>PFLEGEHINWEISE</strong></span></p><p></p><ul><li><p>- <em>Waschbar bei 30°C</em></p></li><li><p><em>- Auf links waschen</em></p></li><li><p><em>- Nicht bleichen</em></p></li><li><p><em>- Nicht in den Trockner</em></p></li><li><p><em>- Auf links bügeln</em></p></li><li><p><em>- Nicht chemisch reinigen</em></p></li></ul><ul><li><p></p></li></ul><p></p><p></p><p></p><p></p></div>';
    }
}
