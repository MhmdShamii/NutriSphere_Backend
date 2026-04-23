<?php

namespace App\Services;

use App\Models\UserWeightLog;

class AnalyticsService
{
    public function __construct(private WeightService $weightService) {}

    public function logWeight(int $userId, float $weightKg, ?string $note = null, ?\DateTimeInterface $date = null): UserWeightLog
    {
        return $this->weightService->logWeight($userId, $weightKg, $note, $date);
    }

    public function getWeightHistory(int $userId, ?string $from = null, ?string $to = null)
    {
        return $this->weightService->getHistory($userId, $from, $to);
    }
}
