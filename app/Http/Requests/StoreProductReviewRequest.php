<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensures only authenticated users can submit a review.
        // return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['required', 'string', 'min:10'],
            // This rule ensures a user can only review a specific product once.
            'user_id' => [
                Rule::unique('product_reviews')->where('product_id', $this->route('product')->id)
            ],
        ];
    }

    /**
     * Get the custom validation messages for the defined rules.
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'You have already submitted a review for this product.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This merges the authenticated user's ID into the request data,
     * allowing us to use it in the validation rules.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['user_id' => $this->user()->id]);
    }
}
