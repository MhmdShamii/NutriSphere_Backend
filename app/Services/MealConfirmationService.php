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
        if ($meal->confirmed_at !== null) {
            $meal->ingredients()->detach();
            $meal->preparationSteps()->delete();
            $meal->delete();
        } else {
            $meal->forceDelete();
        }

        return ['meal_post_id' => $meal->id];
    }
}
