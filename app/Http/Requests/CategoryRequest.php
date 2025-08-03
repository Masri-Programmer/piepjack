<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('categories', 'name')->ignore($this->category?->id),
            ],
            'active' => ['required', 'boolean'],
            'promoted' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }
}
