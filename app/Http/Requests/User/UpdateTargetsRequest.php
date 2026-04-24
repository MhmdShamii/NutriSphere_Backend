<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTargetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'daily_calorie_target' => 'sometimes|integer|min:500|max:10000',
            'daily_protein_g'      => 'sometimes|integer|min:0',
            'daily_carbs_g'        => 'sometimes|integer|min:0',
            'daily_fat_g'          => 'sometimes|integer|min:0',
        ];
    }
}
