<?php

namespace App\Services;

use App\Models\UserWeightLog;
use Illuminate\Support\Facades\Date;

class WeightService
{
    public function logWeight(int $userId, float $weightKg, ?string $note = null, ?\DateTimeInterface $date = null): UserWeightLog
    {
        return UserWeightLog::updateOrCreate(
            ['user_id' => $userId, 'logged_at' => $date ?? Date::today()],
            ['weight_kg' => $weightKg, 'note' => $note]
        );
    }

    public function getHistory(int $userId, ?string $from = null, ?string $to = null)
    {
        return UserWeightLog::where('user_id', $userId)
            ->when($from, fn($q) => $q->whereDate('logged_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('logged_at', '<=', $to))
            ->orderBy('logged_at', 'desc')
            ->get();
    }
}
