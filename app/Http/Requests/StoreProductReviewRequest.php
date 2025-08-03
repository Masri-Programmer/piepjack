<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['required', 'string'],
        ];
    }
}
