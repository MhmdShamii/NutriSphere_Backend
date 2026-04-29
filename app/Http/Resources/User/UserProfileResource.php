<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'date_of_birth'        => $this->date_of_birth?->format('Y-m-d'),
            'gender'               => $this->gender,
            'weight_kg'            => $this->weight_kg,
            'height_cm'            => $this->height_cm,
            'activity_level'       => $this->activity_level,
            'goal'                 => $this->goal,
            'dietary_preferences'  => $this->dietary_preferences,
            'daily_calorie_target' => $this->daily_calorie_target,
            'daily_protein_g'      => $this->daily_protein_g,
            'daily_carbs_g'        => $this->daily_carbs_g,
            'daily_fat_g'          => $this->daily_fat_g,
        ];
    }
}
