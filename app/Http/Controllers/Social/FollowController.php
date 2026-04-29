<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Resources\Social\FollowUserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Social\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    use ApiResponse;

    public function __construct(private FollowService $followService) {}

    public function follow(User $user): JsonResponse
    {
        try {
            $this->followService->follow(Auth::user(), $user);

            return $this->success(message: 'User followed successfully.', status: 201);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 409);
        }
    }

    public function unfollow(User $user): JsonResponse
    {
        try {
            $this->followService->unfollow(Auth::user(), $user);

            return $this->success(message: 'User unfollowed successfully.');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    public function followers(User $user): JsonResponse
    {
        $result = $this->followService->getFollowers($user);

        return $this->paginated(
            FollowUserResource::collection($result),
            [
                'current_page' => $result->currentPage(),
                'last_page'    => $result->lastPage(),
                'per_page'     => $result->perPage(),
                'total'        => $result->total(),
            ],
            'Followers retrieved.'
        );
    }

    public function following(User $user): JsonResponse
    {
        $result = $this->followService->getFollowing($user);

        return $this->paginated(
            FollowUserResource::collection($result),
            [
                'current_page' => $result->currentPage(),
                'last_page'    => $result->lastPage(),
                'per_page'     => $result->perPage(),
                'total'        => $result->total(),
            ],
            'Following retrieved.'
        );
    }
}
