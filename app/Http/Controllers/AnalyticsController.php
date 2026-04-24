<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaloriesWeekRequest;
use App\Http\Requests\LogWeightRequest;
use App\Http\Resources\CaloriesDayResource;
use App\Http\Resources\DaySummaryResource;
use App\Http\Resources\MacrosDayResource;
use App\Http\Resources\WeightLogResource;
use App\Http\Responses\ApiResponse;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function __construct(private AnalyticsService $analyticsService) {}

    public function logWeight(LogWeightRequest $request)
    {
        $data = $request->validated();

        $log = $this->analyticsService->logWeight(
            userId: Auth::id(),
            weightKg: $data['weight_kg'],
            note: $data['note'] ?? null,
            date: isset($data['logged_at']) ? Carbon::parse($data['logged_at']) : null,
        );

        return $this->success(new WeightLogResource($log), 'Weight logged successfully.', status: 201);
    }

    public function caloriesWeek(CaloriesWeekRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $days = $this->analyticsService->getCaloriesWeek(
            userId: $user->id,
            start: $data['start'],
            end: $data['end'],
            profileCreatedAt: $user->created_at,
        );

        return $this->success(CaloriesDayResource::collection(collect($days)), 'Calories week retrieved.');
    }

    public function macrosWeek(CaloriesWeekRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $days = $this->analyticsService->getMacrosWeek(
            userId: $user->id,
            start: $data['start'],
            end: $data['end'],
            profileCreatedAt: $user->created_at,
        );

        return $this->success(MacrosDayResource::collection(collect($days)), 'Macros week retrieved.');
    }

    public function todayLogs()
    {
        $date    = Carbon::today()->toDateString();
        $summary = $this->analyticsService->getTodayLogs(Auth::id());

        return $this->success(new DaySummaryResource($summary, $date), "Today's logs retrieved.");
    }

    public function dayLogs(Request $request)
    {
        $request->validate(['date' => 'required|date_format:Y-m-d']);

        $date    = $request->input('date');
        $summary = $this->analyticsService->getDayLogs(Auth::id(), $date);

        return $this->success(new DaySummaryResource($summary, $date), 'Day logs retrieved.');
    }

    public function streak()
    {
        $streak = $this->analyticsService->getCurrentStreak(Auth::id());

        return $this->success(['current_streak' => $streak], 'Streak retrieved.');
    }

    public function weightHistory(Request $request)
    {
        $user = Auth::user();

        $logs = $this->analyticsService->getWeightHistory(
            userId: $user->id,
            from: $request->query('from'),
            to: $request->query('to'),
        );

        return $this->success(WeightLogResource::collection($logs), 'Weight history retrieved.');
    }
}
