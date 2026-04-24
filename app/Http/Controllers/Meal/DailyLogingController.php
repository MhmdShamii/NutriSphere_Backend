<?php

namespace App\Http\Controllers\Meal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meal\CustomMealRequest;
use App\Http\Requests\Meal\EstimateMealRequest;
use App\Http\Resources\Meal\DailyLogResource;
use App\Http\Resources\Meal\MealLogResponseResource;
use App\Http\Responses\ApiResponse;
use App\Models\DailyLog;
use App\Models\MealPost;
use App\Services\Meal\DailyLogingService;
use App\Services\Meal\HealthWarningService;
use Illuminate\Support\Facades\Auth;

class DailyLogingController extends Controller
{
    use ApiResponse;

    public function __construct(
        private DailyLogingService $dailyLogingService,
        private HealthWarningService $healthWarningService,
    ) {}

    public function logMeal(MealPost $meal)
    {
        abort_if($meal->confirmed_at === null, 422, 'Meal is not confirmed yet.');

        $user    = Auth::user();
        $log     = $this->dailyLogingService->logMealFromPost($meal, $user);
        $warning = $this->healthWarningService->fromMealPost($user, $meal);

        return $this->success(new MealLogResponseResource($log, $warning), 'Meal logged successfully.', 'data', 201);
    }

    public function logCustomMeal(CustomMealRequest $request)
    {
        $user    = Auth::user();
        $data    = $request->validated();
        $log     = $this->dailyLogingService->logCustomMeal($user, $data);
        $warning = $this->healthWarningService->fromIngredients($user, $data['ingredients']);

        return $this->success(new MealLogResponseResource($log, $warning), 'Review your meal macros before confirming.', 'data', 202);
    }

    public function logEstimatedMeal(EstimateMealRequest $request)
    {
        $user    = Auth::user();
        $data    = $request->validated();
        $log     = $this->dailyLogingService->logEstimatedMeal($user, $data);
        $warning = $this->healthWarningService->fromMealName($user, $data['name'], $data['description'] ?? null);

        return $this->success(new MealLogResponseResource($log, $warning), 'Review your meal macros before confirming.', 'data', 202);
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
