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
            'products.*.id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $variant = $this->productVariants->get($value);

                    if (! $variant) {
                        $index = explode('.', $attribute)[1];
                        $name = $this->input("products.{$index}.name") ?? (__('Product').' #'.($index + 1));
                        $fail(__('The product ":name" is no longer available.', ['name' => $name]));
                    }
                },
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $variantId = $this->input("products.{$index}.id");

                    $variant = $this->productVariants->get($variantId);

                    if ($variant) {
                        $name = $variant->product->translateAttribute('name') ?? $this->input("products.{$index}.name");
                        if ($value > $variant->stock) {
                            $fail(__('The requested quantity for ":name" exceeds the available stock of :stock.', [
                                'name' => $name,
                                'stock' => $variant->stock,
                            ]));
                        }
                    }
                },
            ],

            'promo_code' => ['nullable', 'string'],
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
            'products.*.id.required' => __('A product ID is missing'),
            'shipping_method_id.required' => __('Please select a shipping method'),
        ];
    }

    public function attributes(): array
    {
        $attributes = [];

        if ($this->has('products')) {
            foreach ($this->input('products') as $index => $product) {
                $attributes["products.{$index}.id"] = __('Product').' #'.($index + 1);
                $attributes["products.{$index}.quantity"] = __('Quantity').' #'.($index + 1);
            }
        }

        return $attributes;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('products')) {
            $variantIds = collect($this->input('products'))->pluck('id')->all();
            $this->productVariants = ProductVariant::with('product')->whereIn('id', $variantIds)->get()->keyBy('id');
        }
    }
}
