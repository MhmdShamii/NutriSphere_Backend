<?php

namespace App\Services;

use App\Models\DailySummary;
use App\Models\UserWeightLog;
use Illuminate\Support\Carbon;

class AnalyticsService
{
    public function __construct(private WeightService $weightService) {}

    public function logWeight(int $userId, float $weightKg, ?string $note = null, ?\DateTimeInterface $date = null): UserWeightLog
    {
        return $this->weightService->logWeight($userId, $weightKg, $note, $date);
    }

    public function getCaloriesWeek(int $userId, string $start, string $end, Carbon $profileCreatedAt): array
    {
        $summaries = DailySummary::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn($s) => $s->date->toDateString());

        $profileStart = $profileCreatedAt->startOfDay();
        $days = [];
        $current = Carbon::parse($start);
        $last = Carbon::parse($end);

        while ($current->lte($last)) {
            $key = $current->toDateString();

            if ($current->lt($profileStart)) {
                $days[] = ['date' => $key, 'calories_consumed' => null, 'calories_target' => null];
            } else {
                $summary = $summaries->get($key);
                $days[] = [
                    'date'              => $key,
                    'calories_consumed' => $summary ? (float) $summary->calories_consumed : 0,
                    'calories_target'   => $summary ? (float) $summary->calories_target : null,
                ];
            }

            $current->addDay();
        }

        return $days;
    }

    public function getWeightHistory(int $userId, ?string $from = null, ?string $to = null)
    {
        return $this->weightService->getHistory($userId, $from, $to);
    }
}
