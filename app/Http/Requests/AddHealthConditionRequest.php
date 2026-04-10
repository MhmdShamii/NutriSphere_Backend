<?php

namespace App\Http\Requests;

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
            'health_condition_id' => ['nullable', 'integer', 'exists:health_conditions,id', 'required_without:custom_condition'],
            'custom_condition'    => ['nullable', 'string', 'max:255', 'required_without:health_condition_id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $conditionId = $this->input('health_condition_id');
            $custom      = $this->input('custom_condition');

            if ($conditionId && $custom) {
                $validator->errors()->add('health_condition_id', 'Provide either a predefined condition or a custom one, not both.');
            }

            if ($conditionId && $this->user()->healthConditions()->where('health_condition_id', $conditionId)->exists()) {
                $validator->errors()->add('health_condition_id', 'This condition is already added.');
            }
        });
    }
}
