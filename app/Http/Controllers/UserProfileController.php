<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileBasicRequest;
use App\Http\Requests\UserProfileCompleteRequest;
use App\Http\Resources\UserProfileResource;
use App\Http\Responses\ApiResponse;
use App\Services\UserProfileService;
use Illuminate\Http\JsonResponse;

class UserProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private UserProfileService $userProfileService) {}

    public function completeBasicInfo(UserProfileBasicRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->completeBasicInfo($request->user(), $request->validated());

        return $this->success(new UserProfileResource($profile), 'Basic info completed successfully', dataKey: 'profile');
    }

    public function completeTargets(UserProfileCompleteRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->completeTargets($request->user(), $request->validated());

        return $this->success(new UserProfileResource($profile), 'Targets set successfully', dataKey: 'profile');
    }
}
