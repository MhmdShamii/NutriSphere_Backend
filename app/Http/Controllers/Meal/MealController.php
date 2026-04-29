<?php

namespace App\Http\Controllers\Meal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meal\ConfirmMealRequest;
use App\Http\Requests\Meal\CreateMealRequest;
use App\Http\Resources\Meal\MealPostDetailResource;
use App\Http\Resources\Meal\MealPostResource;
use App\Http\Resources\Meal\MealPostResponseResource;
use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use Illuminate\Support\Facades\Auth;
use App\Services\Meal\CreateMealService;
use App\Services\Meal\HealthWarningService;
use App\Services\Meal\MealConfirmationService;
use App\Services\Meal\MealQueryService;
use Illuminate\Http\JsonResponse;

class MealController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CreateMealService $createMealService,
        private MealConfirmationService $mealConfirmationService,
        private HealthWarningService $healthWarningService,
        private MealQueryService $mealQueryService,
    ) {}

    public function show(int $id): JsonResponse
    {
        $meal = $this->mealQueryService->getById($id, Auth::user());

        return $this->success(new MealPostDetailResource($meal), 'Meal retrieved successfully.', 'meal');
    }

    public function store(CreateMealRequest $request): JsonResponse
    {
        $user     = Auth::user();
        $mealPost = $this->createMealService->create($user->profile, $request->validated());
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
