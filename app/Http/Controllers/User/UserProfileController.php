<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateTargetsRequest;
use App\Http\Requests\User\UserProfileBasicRequest;
use App\Http\Requests\User\UserProfileCompleteRequest;
use App\Http\Resources\User\UserProfileResource;
use App\Http\Responses\ApiResponse;
use App\Services\User\UserProfileService;
use Illuminate\Http\JsonResponse;

class UserProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private UserProfileService $userProfileService) {}

    public function storeBasicInfo(UserProfileBasicRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->completeBasicInfo($request->user(), $request->validated());

        return $this->success(new UserProfileResource($profile), 'Basic info completed successfully', dataKey: 'profile');
    }

    public function storeTargets(UserProfileCompleteRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->completeTargets($request->user(), $request->validated());

        return $this->success(new UserProfileResource($profile), 'Targets set successfully', dataKey: 'profile');
    }

    public function updateTargets(UpdateTargetsRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->updateTargets($request->user(), $request->validated());

        return $this->success(new UserProfileResource($profile), 'Targets updated successfully', dataKey: 'profile');
    }
}
