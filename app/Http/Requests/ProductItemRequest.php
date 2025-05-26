<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => 'required|exists:products,id', // Only required for store
            'quantity' => 'required|integer',
            'image' => 'nullable|string',
            'image_mime' => 'nullable|string',
            'image_size' => 'nullable',
            'price' => 'required|numeric',
            'active' => 'required|boolean',
            'variations' => 'array|required',
        ];

        return $rules;
    }

    /**
     * Configure custom validation messages (optional).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'The product ID is required.',
            'product_id.exists' => 'The selected product ID does not exist.',
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a numeric value.',
            'active.required' => 'The active status is required.',
            'active.boolean' => 'The active status must be a boolean value.',
            'variations.array' => 'The variations must be an array.',
            'variations.*.exists' => 'One or more variation options are invalid.',
        ];
    }

    /**
     * Custom validation logic for additional checks.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Fetch product and category
            $product_id = $this->input('product_id');
            $product = \App\Models\Product::findOrFail($product_id);
            $category_id = $product->category_id;

            // Fetch valid variation IDs for this category
            $variation_ids = \App\Models\Variation::where('category_id', $category_id)->pluck('id');

            // Validate if the variation count matches the expected count
            $variations = $this->input('variations');
            if (count($variations) !== $variation_ids->count()) {
                $validator->errors()->add('variations', 'Invalid number of variations.');
            }

            // Validate each variation
            foreach ($variations as $variation) {
                $variation_option = \App\Models\VariationOption::findOrFail($variation);
                if (!in_array($variation_option->variation_id, $variation_ids->toArray())) {
                    $validator->errors()->add('variations', 'Invalid variation option.');
                }
            }

            // Check for duplicate product items with the same variations
            if ($this->route('id')) { // For update requests
                $existingItem = \App\Models\ProductItem::where('product_id', $product_id)
                    ->where('id', '!=', $this->route('id'))
                    ->whereHas('configurations', function ($query) use ($variations) {
                        $query->whereIn('variation_option_id', $variations)
                            ->groupBy('product_item_id')
                            ->havingRaw('COUNT(DISTINCT variation_option_id) = ?', [count($variations)]);
                    })
                    ->first();

                if ($existingItem) {
                    $validator->errors()->add('variations', 'Another product item with the same variations already exists.');
                }
            } else { // For store requests
                $existingItem = \App\Models\ProductItem::where('product_id', $product_id)
                    ->whereHas('configurations', function ($query) use ($variations) {
                        $query->whereIn('variation_option_id', $variations)
                            ->groupBy('product_item_id')
                            ->havingRaw('COUNT(DISTINCT variation_option_id) = ?', [count($variations)]);
                    })
                    ->first();

                if ($existingItem) {
                    $validator->errors()->add('variations', 'Product item with the same variations already exists.');
                }
            }
        });
    }
}
