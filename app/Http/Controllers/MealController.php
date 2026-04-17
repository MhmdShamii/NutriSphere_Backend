<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmMealRequest;
use App\Http\Requests\CreateMealRequest;
use App\Http\Resources\MealPostResource;
use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use App\Services\CreateMealService;
use App\Services\MealConfirmationService;
use Illuminate\Http\JsonResponse;

class MealController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CreateMealService $CreateMealService,
        private MealConfirmationService $mealConfirmationService,
    ) {}

    public function store(CreateMealRequest $request): JsonResponse
    {
        $profile = auth()->user()->profile;

        $mealPost = $this->CreateMealService->create($profile, $request->validated());

        return $this->success(new MealPostResource($mealPost), "Review your meal before confirming", "meal", 202);
    }

    public function confirm(ConfirmMealRequest $request, MealPost $meal): JsonResponse
    {
        $result = $this->mealConfirmationService->confirm($meal, auth()->user()->profile, $request->file('image'));

        if (is_array($result) && isset($result['error'])) {
            return $this->error($result['error'], $result['status']);
        }

        return $this->success(new MealPostResource($result), 'Meal post is now live', 'meal', 200);
    }

    public function discard(MealPost $meal): JsonResponse
    {
        $result = $this->mealConfirmationService->discard($meal, auth()->user()->profile);

        if (isset($result['error'])) {
            return $this->error($result['error'], $result['status']);
        }

        return $this->success($result, 'Meal post discarded', 'data', 200);
    }
}
