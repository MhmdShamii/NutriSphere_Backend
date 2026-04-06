<?php

namespace App\Http\Requests;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('country_code')) {
            $country = Country::findByCode(strtoupper($this->country_code))->first();
            $this->merge(['country_id' => $country?->id]);
        }
    }

    public function rules(): array
    {
        return [
            'first_name'   => 'sometimes|string|max:255',
            'last_name'    => 'sometimes|string|max:255',
            'country_code' => 'sometimes|string|size:3|exists:countries,code',
            'country_id'   => 'sometimes|integer|exists:countries,id',
        ];
    }

    public function messages(): array
    {
        return [
            'country_id.exists' => 'The country code is invalid or does not exist.',
        ];
    }
}
