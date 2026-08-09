<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAvisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $recipe = $this->route('recipe');

                if ($recipe->avis()->where('user_id', $this->user()->id)->exists()) {
                    $validator->errors()->add('rating', 'You have already reviewed this recipe.');
                }
            },
        ];
    }
}
