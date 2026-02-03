<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            $this->returnUserResource($this->userService->returnUser($request)),
            'User retrieved successfully'
        );
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {

        return $this->success(
            $this->returnUserResource($this->userService->updateUserAvatar($request->user(), $request->file('avatar'))),
            "User Avatar Updated Successfuly"
        );
    }

    public function deleteAvatar(Request $request): JsonResponse
    {

        return $this->success(
            $this->returnUserResource($this->userService->updateUserAvatar($request->user(), null)),
            "User Avatar deleted Successfuly"
        );
    }

    // ====== Helper Function ======
    private function returnUserResource(User $user)
    {
        return ['user' => new UserResource($user)];
    }
}
