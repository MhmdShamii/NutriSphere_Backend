<?php

namespace App\Services;

use App\Models\MealPost;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MealConfirmationService
{
    public function confirm(MealPost $meal, UserProfile $profile, UploadedFile $image): MealPost|array
    {
        if ($meal->user_profile_id !== $profile->id) {
            return ['error' => 'Forbidden', 'status' => 403];
        }

        if ($meal->confirmed_at !== null) {
            return ['error' => 'Meal is already confirmed', 'status' => 409];
        }

        $path = $image->store('meals', 'public');
        $url  = Storage::disk('public')->url($path);

        $meal->update([
            'image_url'    => $url,
            'confirmed_at' => now(),
        ]);

        $meal->load(['ingredients', 'mealMacro', 'preparationSteps']);

        return $meal;
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
