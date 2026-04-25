<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Meal\MealCardResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Meal\MealQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserMealController extends Controller
{
    use ApiResponse;

    public function __construct(private MealQueryService $mealQueryService) {}

    public function publicMeals(User $user): JsonResponse
    {
        $result = $this->mealQueryService->getUserPublicMeals($user, Auth::user());

        return $this->paginated(
            MealCardResource::collection($result),
            [
                'next_cursor' => $result->nextCursor()?->encode(),
                'prev_cursor' => $result->previousCursor()?->encode(),
                'per_page'    => $result->perPage(),
            ],
            'Meals retrieved.'
        );
    }

    public function privateMeals(User $user): JsonResponse
    {
        if (Auth::id() !== $user->id) {
            return $this->error('You are not authorized to view these meals.', 403);
        }

        $result = $this->mealQueryService->getUserPrivateMeals($user);

        return $this->paginated(
            MealCardResource::collection($result),
            [
                'next_cursor' => $result->nextCursor()?->encode(),
                'prev_cursor' => $result->previousCursor()?->encode(),
                'per_page'    => $result->perPage(),
            ],
            'Meals retrieved.'
        );
    }
}
