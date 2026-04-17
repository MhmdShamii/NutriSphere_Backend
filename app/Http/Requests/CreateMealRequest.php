<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:1000'],
            'visibility'          => ['required', 'in:public,private'],
            'image'               => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'ingredients'         => ['required', 'array', 'min:1', 'max:20'],
            'ingredients.*.name'  => ['required', 'string', 'max:255'],
            'ingredients.*.portion' => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'ingredients.*.unit'  => ['required', 'string', 'max:50'],
            'servings'            => ['required', 'integer', 'min:1', 'max:100'],
            'preparation_steps'               => ['nullable', 'array', 'max:20'],
            'preparation_steps.*.description' => ['required_with:preparation_steps', 'string', 'max:1000'],
        ];
    }
}
