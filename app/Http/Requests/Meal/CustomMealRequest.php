<?php

namespace App\Http\Requests\Meal;

use Illuminate\Foundation\Http\FormRequest;

class CustomMealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'ingredients'          => 'required|array|min:1|max:20',
            'ingredients.*.name'   => 'required|string|max:255',
            'ingredients.*.portion' => 'required|numeric|min:0.1|max:10000',
            'ingredients.*.unit'   => 'required|string',
        ];
    }
}
