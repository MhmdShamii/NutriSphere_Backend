<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use App\Services\Social\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    use ApiResponse;

    public function __construct(private LikeService $likeService) {}

    public function like(MealPost $meal): JsonResponse
    {
        try {
            $this->likeService->like(Auth::user(), $meal);

            return $this->success(message: 'Meal liked successfully.', status: 201);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 409);
        }
    }

    public function unlike(MealPost $meal): JsonResponse
    {
        try {
            $this->likeService->unlike(Auth::user(), $meal);

            return $this->success(message: 'Meal unliked successfully.');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }
}
