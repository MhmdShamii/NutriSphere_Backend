<?php

namespace App\Services;

use App\Models\MealPost;
use App\Models\UserProfile;

class MealConfirmationService
{
    public function confirm(MealPost $meal, UserProfile $profile): array
    {
        if ($meal->user_profile_id !== $profile->id) {
            return ['error' => 'Forbidden', 'status' => 403];
        }

        if ($meal->confirmed_at !== null) {
            return ['error' => 'Meal is already confirmed', 'status' => 409];
        }

        $meal->update(['confirmed_at' => now()]);

        return ['meal_post_id' => $meal->id];
    }

    public function discard(MealPost $meal, UserProfile $profile): array
    {
        if ($meal->user_profile_id !== $profile->id) {
            return ['error' => 'Forbidden', 'status' => 403];
        }

        if ($meal->confirmed_at !== null) {
            return ['error' => 'Cannot discard a confirmed meal', 'status' => 409];
        }

        $meal->delete();

        return ['meal_post_id' => $meal->id];
    }
}
