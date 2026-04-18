<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\MealPost;
use App\Services\DailyLogingService;
use Illuminate\Support\Facades\Auth;

class DailyLogingController extends Controller
{
    use ApiResponse;

    public function __construct(private DailyLogingService $dailyLogingService) {}

    public function logMeal(MealPost $meal)
    {
        $log = $this->dailyLogingService->logMealFromPost($meal, Auth::user());

        return $this->success($log, 'Meal logged successfully.', "logged_meal", status: 201);
    }
}
