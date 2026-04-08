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
                        $fail(__('The selected variant is invalid.'));

                        return;
                    }

                    if ($value > $variant->stock) {
                        $fail(__('The requested quantity exceeds the available stock of :stock.', ['stock' => $variant->stock]));
                    }
                },
            ],

            'promo_code' => 'nullable|string|max:6',
            'shipping_method_id' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Please enter an email address'),
            'email.email' => __('Enter a valid email address'),
            'first_name.required' => __('Please enter a first name'),
            'last_name.required' => __('Please enter a last name'),
            'street_address.required' => __('Please enter an address'),
            'city.required' => __('Please enter a city'),
            'state_province.required' => __('Please enter a state'),
            'postal_code.required' => __('Please enter a zip code'),
            'country_code.required' => __('Please select a country'),
            'country_code.exists' => __('Please select a valid country'),
            'billing_same_as_shipping.required' => __('Please specify if billing is same as shipping'),
            'products.required' => __('Your cart is empty'),
            'products.min' => __('Your cart is empty'),
            'shipping_method_id.required' => __('Please select a shipping method'),
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
