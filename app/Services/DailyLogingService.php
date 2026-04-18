<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\DailySummary;
use App\Models\MealPost;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DailyLogingService
{
    public function logMealFromPost(MealPost $mealPost, User $user): DailyLog
    {
        return DB::transaction(function () use ($mealPost, $user) {
            $today = now()->toDateString();

            $profile = $user->profile;

            $summary = DailySummary::firstOrCreate(
                ['user_id' => $user->id, 'date' => $today],
                [
                    'calories_target' => $profile?->daily_calorie_target ?? 0,
                    'protein_target'  => $profile?->daily_protein_g ?? 0,
                    'carbs_target'    => $profile?->daily_carbs_g ?? 0,
                    'fats_target'     => $profile?->daily_fat_g ?? 0,
                    'fiber_target'    => 0,
                ]
            );

            return $this->addLogedMealToDailyLog($mealPost, $user, $summary);
        });
    }

    public function removeLogFromDailySummary(DailyLog $log): void
    {
        DB::transaction(function () use ($log) {
            $summary = $log->dailySummary;

            if ($summary) {
                $this->modifyDailySummary($summary, $log, isAdding: false);
            }

            $log->delete();
        });
    }


    public function addLogedMealToDailyLog(MealPost $mealPost, User $user, DailySummary $summary): DailyLog
    {
        $portionMacros = $this->calculateForOnePortion($mealPost);

        $log = DailyLog::create([
            'user_id'          => $user->id,
            'daily_summary_id' => $summary->id,
            'logged_at'        => now(),
            'type'             => 'meal',
            'meal_post_id'     => $mealPost->id,
            'log_name'         => $mealPost->name,
            'fingerprint'      => $mealPost->fingerprint,
            'calories'         => $portionMacros['calories'],
            'protein'          => $portionMacros['protein'],
            'carbs'            => $portionMacros['carbs'],
            'fats'             => $portionMacros['fats'],
            'fiber'            => $portionMacros['fiber'],
        ]);

        $this->modifyDailySummary($summary, $log, isAdding: true);

        return $log;
    }

    private function calculateForOnePortion(MealPost $mealPost): array
    {
        $macro    = $mealPost->mealMacro;
        $servings = $mealPost->servings ?: 1;

        return [
            'calories' => ($macro?->calories ?? 0) / $servings,
            'protein'  => ($macro?->protein ?? 0) / $servings,
            'carbs'    => ($macro?->carbs ?? 0) / $servings,
            'fats'     => ($macro?->fats ?? 0) / $servings,
            'fiber'    => ($macro?->fiber ?? 0) / $servings,
        ];
    }

    private function modifyDailySummary(DailySummary $summary, DailyLog $log, bool $isAdding = true): void
    {

        if ($isAdding) {
            $summary->increment('calories_consumed', $log->calories);
            $summary->increment('protein_consumed', $log->protein);
            $summary->increment('carbs_consumed', $log->carbs);
            $summary->increment('fats_consumed', $log->fats);
            $summary->increment('fiber_consumed', $log->fiber);
            $summary->increment('logs_count', 1);
        } else {
            $summary->decrement('calories_consumed',  $log->calories);
            $summary->decrement('protein_consumed', $log->protein);
            $summary->decrement('carbs_consumed', $log->carbs);
            $summary->decrement('fats_consumed', $log->fats);
            $summary->decrement('fiber_consumed', $log->fiber);
            $summary->decrement('logs_count', 1);
        }
    }
}
