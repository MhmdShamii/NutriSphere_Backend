<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'daily_calorie_target' => 'required|integer|min:500|max:10000',
            'daily_protein_g'      => 'required|integer|min:0',
            'daily_carbs_g'        => 'required|integer|min:0',
            'daily_fat_g'          => 'required|integer|min:0',
        ];
    }
}
