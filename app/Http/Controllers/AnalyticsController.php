<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaloriesWeekRequest;
use App\Http\Requests\LogWeightRequest;
use App\Http\Resources\CaloriesDayResource;
use App\Http\Resources\MacrosDayResource;
use App\Http\Resources\WeightLogResource;
use App\Http\Responses\ApiResponse;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function __construct(private AnalyticsService $analyticsService) {}

    public function logWeight(LogWeightRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $log = $this->analyticsService->logWeight(
            userId: $user->id,
            weightKg: $data['weight_kg'],
            note: $data['note'] ?? null,
            date: isset($data['logged_at']) ? new \DateTime($data['logged_at']) : null,
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

    public function weightHistory(\Illuminate\Http\Request $request)
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
