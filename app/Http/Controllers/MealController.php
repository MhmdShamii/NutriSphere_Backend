<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmMealRequest;
use App\Http\Requests\CreateMealRequest;
use App\Http\Resources\MealPostResource;
use App\Http\Resources\MealPostResponseResource;
use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use Illuminate\Support\Facades\Auth;
use App\Services\CreateMealService;
use App\Services\HealthWarningService;
use App\Services\MealConfirmationService;
use Illuminate\Http\JsonResponse;

class MealController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CreateMealService $CreateMealService,
        private MealConfirmationService $mealConfirmationService,
        private HealthWarningService $healthWarningService,
    ) {}

    public function store(CreateMealRequest $request): JsonResponse
    {
        $user     = Auth::user();
        $mealPost = $this->CreateMealService->create($user->profile, $request->validated());
        $warning  = $this->healthWarningService->fromMealPost($user, $mealPost);

        return $this->success(new MealPostResponseResource($mealPost, $warning), 'Review your meal before confirming', 'data', 202);
    }

    public function confirm(ConfirmMealRequest $request, MealPost $meal): JsonResponse
    {
        $result = $this->mealConfirmationService->confirm($meal, Auth::user()->profile, $request->file('image'));

        if (is_array($result) && isset($result['error'])) {
            return $this->error($result['error'], $result['status']);
        }

        return $this->success(new MealPostResource($result), 'Meal post is now live', 'meal', 200);
    }

    public function discard(MealPost $meal): JsonResponse
    {
        $result = $this->mealConfirmationService->discard($meal, Auth::user()->profile);

        if (isset($result['error'])) {
            return $this->error($result['error'], $result['status']);
        }

        return $this->success($result, 'Meal post discarded', 'data', 200);
    }
}
