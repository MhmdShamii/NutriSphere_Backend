<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\DailyLog;
use App\Models\MealPost;
use App\Services\DailyLogingService;
use Illuminate\Support\Facades\Auth;

class DailyLogingController extends Controller
{
    use ApiResponse;

    public function __construct(private DailyLogingService $dailyLogingService) {}

    public function logMeal(MealPost $meal)
    {
        abort_if($meal->confirmed_at === null, 422, 'Meal is not confirmed yet.');

        $log = $this->dailyLogingService->logMealFromPost($meal, Auth::user());

        return $this->success($log, 'Meal logged successfully.', "logged_meal", status: 201);
    }

    public function removeDailyLog(DailyLog $log)
    {
        $log = Auth::user()->dailyLogs()->findOrFail($log->id);

        $this->dailyLogingService->removeLogFromDailySummary($log);

        return $this->success(null, 'Log removed successfully.');
    }
}
