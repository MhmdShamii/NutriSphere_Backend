<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomMealRequest;
use App\Http\Requests\EstimateMealRequest;
use App\Http\Resources\DailyLogResource;
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

        return $this->success(new DailyLogResource($log), 'Meal logged successfully.', 'logged_meal', status: 201);
    }

    public function logCustomMeal(CustomMealRequest $request)
    {
        $log = $this->dailyLogingService->logCustomMeal(Auth::user(), $request->validated());

        return $this->success(new DailyLogResource($log), 'Review your meal macros before confirming.', 'logged_meal', status: 202);
    }

    public function logEstimatedMeal(EstimateMealRequest $request)
    {
        $log = $this->dailyLogingService->logEstimatedMeal(Auth::user(), $request->validated());

        return $this->success(new DailyLogResource($log), 'Review your meal macros before confirming.', 'logged_meal', status: 202);
    }

    public function confirmLog(DailyLog $log)
    {
        abort_if($log->confirmed_at !== null, 409, 'Log is already confirmed.');

        $log = $this->dailyLogingService->confirmPendingLog($log);

        return $this->success(new DailyLogResource($log), 'Meal log confirmed and added to your daily summary.', 'logged_meal', status: 201);
    }

    public function removeDailyLog(DailyLog $log)
    {
        $this->dailyLogingService->removeLogFromDailySummary($log);

        return $this->success(null, 'Log removed successfully.');
    }
}
