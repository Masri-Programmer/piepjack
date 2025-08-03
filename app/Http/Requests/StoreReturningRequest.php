<?php

namespace App\Http\Requests\Returning;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturningRequest extends FormRequest
{
    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:not_requested,requested,approved,rejected',
            'reason' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.order_product_id' => 'required|exists:order_products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The specified order does not exist.',
            'items.required' => 'At least one item must be provided for the return.',
        ];
    }
}