<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingredients' => ['required', 'array', 'min:1', 'max:20'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.quantity' => ['nullable', 'string', 'max:50'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:50'],
            'preferences' => ['nullable', 'string', 'max:500'],
            'constraints' => ['nullable', 'string', 'max:500'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'servings' => ['nullable', 'integer', 'min:1', 'max:100'],
            'difficulty' => ['nullable', 'string', 'in:easy,medium,hard'],
        ];
    }
}
