<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CompleteMainInfoRequest;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdateCoverImageRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\User\UserService;
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

    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $result = $this->userService->findUserEmailExist($request->input('email'));

        return $this->success(
            $result,
            $result ? 'Email already exists' : 'Email is available',
            dataKey: 'existing_user'
        );
    }

    public function show(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($this->userService->returnUser($request)),
            'User retrieved successfully',
            dataKey: 'user'
        );
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        return $this->success(
            new UserResource($this->userService->updateUser($request->user(), $request->validated())),
            'User updated successfully',
            dataKey: 'user'
        );
    }

    public function storeMainInfo(CompleteMainInfoRequest $request): JsonResponse
    {
        $user = $this->userService->updateUser($request->user(), $request->validated());
        $user = $this->userService->completeMainInfo($user);

        return $this->success(
            new UserResource($user),
            'Main info completed successfully',
            dataKey: 'user'
        );
    }

    public function storeAvatar(UpdateAvatarRequest $request): JsonResponse
    {

        return $this->success(
            new UserResource($this->userService->updateUserAvatar($request->user(), $request->file('avatar'))),
            'User Avatar Updated Successfuly',
            dataKey: 'user'
        );
    }

    public function destroyAvatar(Request $request): JsonResponse
    {

        return $this->success(
            new UserResource($this->userService->deleteUserAvatar($request->user())),
            'User Avatar deleted Successfuly',
            dataKey: 'user'
        );
    }

    public function storeCoverImage(UpdateCoverImageRequest $request): JsonResponse
    {
        return $this->success(
            new UserResource($this->userService->updateUserCoverImage($request->user(), $request->file('cover_image'))),
            'Cover image updated successfully',
            dataKey: 'user'
        );
    }

    public function destroyCoverImage(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($this->userService->deleteUserCoverImage($request->user())),
            'Cover image deleted successfully',
            dataKey: 'user'
        );
    }
}
