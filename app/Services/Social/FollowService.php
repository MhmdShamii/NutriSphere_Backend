<?php

namespace App\Services\Social;

use App\Models\User;

class FollowService
{
    public function follow(User $follower, User $target): void
    {
        if ($follower->id === $target->id) {
            throw new \InvalidArgumentException('You cannot follow yourself.');
        }

        if ($follower->following()->where('followed_id', $target->id)->exists()) {
            throw new \RuntimeException('You are already following this user.');
        }

        $follower->following()->attach($target->id);
    }

    public function unfollow(User $follower, User $target): void
    {
        if (!$follower->following()->where('followed_id', $target->id)->exists()) {
            throw new \RuntimeException('You are not following this user.');
        }

        $follower->following()->detach($target->id);
    }

    public function getFollowers(User $user, int $perPage = 20)
    {
        return $user->followers()->paginate($perPage);
    }

    public function getFollowing(User $user, int $perPage = 20)
    {
        return $user->following()->paginate($perPage);
    }
}
