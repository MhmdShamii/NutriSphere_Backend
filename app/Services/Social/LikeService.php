<?php

namespace App\Services\Social;

use App\Models\MealPost;
use App\Models\User;

class LikeService
{
    public function like(User $user, MealPost $meal): void
    {
        if ($meal->likes()->where('user_id', $user->id)->exists()) {
            throw new \RuntimeException('You have already liked this meal.');
        }

        $meal->likes()->attach($user->id);
        $meal->increment('likes_count');
    }

    public function unlike(User $user, MealPost $meal): void
    {
        if (!$meal->likes()->where('user_id', $user->id)->exists()) {
            throw new \RuntimeException('You have not liked this meal.');
        }

        $meal->likes()->detach($user->id);
        $meal->decrement('likes_count');
    }
}
