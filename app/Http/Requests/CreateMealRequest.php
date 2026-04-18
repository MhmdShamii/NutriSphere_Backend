<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                            => 'required|string|max:255',
            'description'                     => 'nullable|string|max:1000',
            'visibility'                      => 'required|in:public,private',
            'servings'                        => 'required|integer|min:1|max:100',
            'ingredients'                     => 'required|array|min:1|max:20',
            'ingredients.*.name'              => 'required|string|max:255',
            'ingredients.*.portion'           => 'required|numeric|min:0.1|max:10000',
            'ingredients.*.unit'              => 'required|string',
            'preparation_steps'               => 'nullable|array|max:20',
            'preparation_steps.*.description' => 'required_with:preparation_steps|string|max:1000',
        ];
    }
}
