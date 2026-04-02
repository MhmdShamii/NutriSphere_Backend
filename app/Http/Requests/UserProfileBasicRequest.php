<?php

namespace App\Http\Requests;

use App\Enums\UserActivityLevels;
use App\Enums\UserDietaryPreferences;
use App\Enums\UserGoal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UserProfileBasicRequest extends FormRequest
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
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'height_cm' => ['required', 'numeric', 'min:0'],
            'activity_level' => ['required', new Enum(UserActivityLevels::class)],
            'goal' => ['required', new Enum(UserGoal::class)],
            'dietary_preferences' => ['required', new Enum(UserDietaryPreferences::class)],
        ];
    }
}
