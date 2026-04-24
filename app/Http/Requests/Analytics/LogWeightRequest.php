<?php

namespace App\Http\Requests\Analytics;

use Illuminate\Foundation\Http\FormRequest;

class LogWeightRequest extends FormRequest
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
            'weight_kg' => ['required', 'numeric', 'min:20', 'max:500'],
            'note'      => ['nullable', 'string', 'max:255'],
            'logged_at' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
