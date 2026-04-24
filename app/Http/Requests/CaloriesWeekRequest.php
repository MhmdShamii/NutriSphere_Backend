<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaloriesWeekRequest extends FormRequest
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
            'start' => ['required', 'date'],
            'end'   => ['required', 'date', 'after:start'],
        ];
    }
}
