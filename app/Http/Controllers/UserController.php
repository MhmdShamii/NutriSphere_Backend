<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
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
            $this->userService->returnUser($request),
            'User retrieved successfully'
        );
    }

    public function updateAvatar(UpdateAvatarRequest $request) {}
}
