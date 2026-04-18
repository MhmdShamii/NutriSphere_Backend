<?php

namespace App\Services;

use App\Models\MealPost;
use App\Models\User;

class DailyLogingService
{
    public function logMealFromPost(MealPost $mealPost, User $user)
    {
        dd("Logging meal {$mealPost->id} for user {$user->id}");
    }
}
