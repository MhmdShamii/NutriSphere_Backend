<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddHealthConditionRequest extends FormRequest
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
            'health_condition_id' => ['nullable', 'integer', 'exists:health_conditions,id'],
            'custom_condition'    => ['nullable', 'string', 'max:255'],
        ];
    }
}
