<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMealRequest;
use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use App\Services\CreateMealService;
use Illuminate\Http\JsonResponse;

class MealController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CreateMealService $CreateMealService,
    ) {}

    public function store(CreateMealRequest $request): JsonResponse
    {
        $profile = auth()->user()->profile;

        $result = $this->CreateMealService->create(
            $profile,
            $request->validated()
        );

        return $this->success($result, "Review your meal before confirming", "meal", 202);
    }

    public function confirm(MealPost $meal): JsonResponse
    {
        if ($meal->user_profile_id !== auth()->user()->profile->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($meal->confirmed_at !== null) {
            return response()->json(['message' => 'Already confirmed'], 409);
        }

        $meal->update(['confirmed_at' => now()]);

        return response()->json([
            'status'  => 'confirmed',
            'message' => 'Meal post is now live',
            'data'    => ['meal_post_id' => $meal->id],
        ], 200);
    }

    public function discard(MealPost $meal): JsonResponse
    {
        if ($meal->user_profile_id !== auth()->user()->profile->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($meal->confirmed_at !== null) {
            return response()->json(['message' => 'Cannot discard a confirmed meal'], 409);
        }

        $meal->delete();

        return response()->json([
            'status'  => 'discarded',
            'message' => 'Meal post discarded',
        ], 200);
    }
}
