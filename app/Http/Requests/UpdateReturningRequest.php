<?php

namespace App\Http\Requests\Returning;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReturningRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'in:not_requested,requested,approved,rejected',
            'reason' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.order_product_id' => 'required_with:items|exists:order_products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ];
    }
}
