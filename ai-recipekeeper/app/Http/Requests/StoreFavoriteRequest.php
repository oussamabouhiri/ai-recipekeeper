<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recette_id' => [
                'required',
                'integer',
                'exists:recettes,id',
                Rule::unique('favoris', 'recette_id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
