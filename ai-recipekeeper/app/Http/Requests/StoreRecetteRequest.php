<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecetteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prep_time' => ['nullable', 'integer', 'min:0'],
            'cook_time' => ['nullable', 'integer', 'min:0'],
            'servings' => ['nullable', 'integer', 'min:1'],
            'difficulty' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string'],
            'statut' => ['sometimes', 'in:published,hidden'],
            'etapes' => ['sometimes', 'array'],
            'etapes.*' => ['array'],
            'etapes.*.step_number' => ['required', 'integer', 'min:1'],
            'etapes.*.instruction' => ['required', 'string'],
            'ingredients' => ['sometimes', 'array'],
            'ingredients.*' => ['array'],
            'ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['nullable', 'string'],
            'ingredients.*.unit' => ['nullable', 'string'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
