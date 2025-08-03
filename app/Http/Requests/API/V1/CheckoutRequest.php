<?php

namespace App\Http\Requests\API\V1;

use App\Models\ProductItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class CheckoutRequest extends FormRequest
{
    // Property to hold product items fetched in a single query
    protected ?Collection $productItems = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state_province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country_code' => 'required|string|max:2',
            'label' => 'nullable|string|max:255',
            'is_default_shipping' => 'sometimes|boolean',
            'is_default_billing' => 'sometimes|boolean',

            'products' => 'required|array',
            'products.*.id' => 'required|exists:product_items,id',
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $productId = $this->input("products.{$index}.id");

                    $productItem = $this->productItems->get($productId);

                    if (!$productItem) {
                        $fail("The selected product (ID: {$productId}) is invalid.");
                        return;
                    }

                    if ($value > $productItem->quantity) {
                        $fail("The requested quantity for product ID {$productId} exceeds the available stock of {$productItem->quantity}.");
                    }
                },
            ],

            'promo_code' => 'nullable|string',
        ];
    }

    /**
     * Prepare the data for validation.
     * OPTIMIZATION: Fetch all product items at once to avoid N+1 problem in validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('products')) {
            $productIds = collect($this->input('products'))->pluck('id')->all();
            $this->productItems = ProductItem::whereIn('id', $productIds)->get()->keyBy('id');
        }
    }
}
