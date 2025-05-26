<?php

namespace App\Http\Requests\API\V1;

use App\Models\ProductItem;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'promo_code' => 'nullable|string',
            'address_line_one' => 'required|string',
            'address_line_two' => 'nullable|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:product_items,id',
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $productId = $this->input(str_replace('.quantity', '.id', $attribute));
                    $availableStock = ProductItem::where('id', $productId)->value('quantity');
                    if ($availableStock === null) {
                        $fail("Product with ID {$productId} does not exist.");
                    } elseif ($value > $availableStock) {
                        $fail("The requested quantity for product ID {$productId} exceeds the available stock.");
                    }
                },
            ],
        ];
    }
}
