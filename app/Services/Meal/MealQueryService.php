<?php

namespace App\Services\Meal;

use App\Enums\MealVisibility;
use App\Models\MealPost;
use App\Models\User;

class MealQueryService
{
    public function getById(int $id, User $viewer): MealPost
    {
        $meal = MealPost::with(['ingredients', 'mealMacro', 'preparationSteps', 'userProfile.user'])
            ->whereNotNull('confirmed_at')
            ->findOrFail($id);

        if ($meal->visibility === MealVisibility::PRIVATE) {
            $ownerId = $meal->userProfile?->user?->id;

            if ($viewer->id !== $ownerId) {
                abort(404);
            }
        }

        return $meal;
    }
}
