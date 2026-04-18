<?php

namespace App\Http\Controllers;

use App\Models\MealPost;
use App\Services\DailyLogingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogingController extends Controller
{
    public function __construct(private DailyLogingService $dailyLogingService) {}
    function logMeal(MealPost $meal)
    {
        $this->dailyLogingService->logMealFromPost($meal, Auth::User());
    }
}
