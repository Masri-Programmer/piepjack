<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Lunar\Models\ProductVariant;

class CheckoutRequest extends FormRequest
{
    // Property to hold product variants fetched in a single query
    protected ?Collection $productVariants = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state_province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:12',
            'country_code' => 'required|string|size:2|exists:lunar_countries,iso2',

            // Billing Info (Multi-step)
            'billing_same_as_shipping' => 'required|boolean',
            'billing_first_name' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_last_name' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_street_address' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_city' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_postal_code' => 'required_if:billing_same_as_shipping,false|nullable|string|max:12',
            'billing_country_code' => 'required_if:billing_same_as_shipping,false|nullable|string|size:2|exists:lunar_countries,iso2',

            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:lunar_product_variants,id',
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $variantId = $this->input("products.{$index}.id");

                    $variant = $this->productVariants->get($variantId);

                    if (! $variant) {
                        $fail("The selected variant (ID: {$variantId}) is invalid.");

                        return;
                    }

                    if ($value > $variant->stock) {
                        $fail("The requested quantity for variant ID {$variantId} exceeds the available stock of {$variant->stock}.");
                    }
                },
            ],

            'promo_code' => 'nullable|string|max:6',
            'shipping_method_id' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('products')) {
            $variantIds = collect($this->input('products'))->pluck('id')->all();
            $this->productVariants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
        }
    }
}
