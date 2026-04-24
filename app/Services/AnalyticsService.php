<?php

namespace App\Services;

use App\Models\DailySummary;
use App\Models\UserWeightLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function __construct(private WeightService $weightService) {}

    public function logWeight(int $userId, float $weightKg, ?string $note = null, ?\DateTimeInterface $date = null): UserWeightLog
    {
        return $this->weightService->logWeight($userId, $weightKg, $note, $date);
    }

    public function getCaloriesWeek(int $userId, string $start, string $end, Carbon $profileCreatedAt): array
    {
        $summaries    = $this->fetchSummariesByRange($userId, $start, $end);
        $profileStart = $profileCreatedAt->copy()->startOfDay();

        return $this->iterateDays($start, $end, function (string $key, bool $beforeProfile) use ($summaries) {
            if ($beforeProfile) {
                return ['date' => $key, 'calories_consumed' => null, 'calories_target' => null];
            }

            $s = $summaries->get($key);
            return [
                'date'              => $key,
                'calories_consumed' => $s ? (float) $s->calories_consumed : 0,
                'calories_target'   => $s ? (float) $s->calories_target   : null,
            ];
        }, $profileStart);
    }

    public function getMacrosWeek(int $userId, string $start, string $end, Carbon $profileCreatedAt): array
    {
        $summaries    = $this->fetchSummariesByRange($userId, $start, $end);
        $profileStart = $profileCreatedAt->copy()->startOfDay();

        return $this->iterateDays($start, $end, function (string $key, bool $beforeProfile) use ($summaries) {
            if ($beforeProfile) {
                return [
                    'date'             => $key,
                    'protein_consumed' => null, 'protein_target' => null,
                    'carbs_consumed'   => null, 'carbs_target'   => null,
                    'fats_consumed'    => null, 'fats_target'    => null,
                ];
            }

            $s = $summaries->get($key);
            return [
                'date'             => $key,
                'protein_consumed' => $s ? (float) $s->protein_consumed : 0,
                'protein_target'   => $s ? (float) $s->protein_target   : null,
                'carbs_consumed'   => $s ? (float) $s->carbs_consumed   : 0,
                'carbs_target'     => $s ? (float) $s->carbs_target     : null,
                'fats_consumed'    => $s ? (float) $s->fats_consumed    : 0,
                'fats_target'      => $s ? (float) $s->fats_target      : null,
            ];
        }, $profileStart);
    }

    public function getWeightHistory(int $userId, ?string $from = null, ?string $to = null)
    {
        return $this->weightService->getHistory($userId, $from, $to);
    }

    public function getTodayLogs(int $userId): ?DailySummary
    {
        return $this->getDayLogs($userId, Carbon::today()->toDateString());
    }

    public function getDayLogs(int $userId, string $date): ?DailySummary
    {
        return DailySummary::where('user_id', $userId)
            ->whereDate('date', $date)
            ->with(['logs.mealPost'])
            ->first();
    }

    public function getTodayMacros(int $userId, \App\Models\UserProfile $profile): DailySummary|\App\Models\UserProfile
    {
        return DailySummary::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->first() ?? $profile;
    }

    public function getCurrentStreak(int $userId): int
    {
        $dates = DailySummary::where('user_id', $userId)
            ->where('logs_count', '>', 0)
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->all();

        if (empty($dates)) {
            return 0;
        }

        $dateSet = array_flip($dates);
        $streak  = 0;
        $check   = Carbon::today();

        // if today has no logs yet, streak can still be alive from yesterday
        if (!isset($dateSet[$check->toDateString()])) {
            $check->subDay();
        }

        while (isset($dateSet[$check->toDateString()])) {
            $streak++;
            $check->subDay();
        }

        return $streak;
    }

    private function fetchSummariesByRange(int $userId, string $start, string $end): Collection
    {
        return DailySummary::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn($s) => $s->date->toDateString());
    }

    private function iterateDays(string $start, string $end, callable $build, Carbon $profileStart): array
    {
        $days    = [];
        $current = Carbon::parse($start);
        $last    = Carbon::parse($end);

        while ($current->lte($last)) {
            $key        = $current->toDateString();
            $days[]     = $build($key, $current->lt($profileStart));
            $current->addDay();
        }

        return $days;
    }
}
