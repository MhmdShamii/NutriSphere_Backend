<?php

namespace App\Services;

use App\Enums\UserActivityLevels;
use App\Enums\UserGender;
use App\Enums\UserGoal;
use App\Enums\UserOnboardingSteps;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class UserProfileService
{
    public function completeBasicInfo($user, $profileData)
    {
        DB::transaction(function () use ($user, $profileData) {
            $user->profile()->update($profileData);

            $profile = $user->profile()->first();
            $targets = $this->estimateTargets($profile);
            $user->profile()->update($targets);

            if ($user->onboarding_step === UserOnboardingSteps::BASIC_INFO) {
                $user->onboarding_step = UserOnboardingSteps::TARGETS;
                $user->save();
            }
        });

        return $user->profile()->first();
    }

    public function completeTargets($user, array $targets)
    {
        DB::transaction(function () use ($user, $targets) {
            $user->profile()->update($targets);

            if ($user->onboarding_step === UserOnboardingSteps::TARGETS) {
                $user->onboarding_step = UserOnboardingSteps::HEALTH_CONDITIONS;
                $user->save();
            }
        });

        return $user->profile()->first();
    }

    //============== Helper Functions =============

    private function estimateTargets(UserProfile $profile): array
    {
        return $profile->body_fat_pct !== null
            ? $this->katchMcArdleEstimation($profile)
            : $this->mifflinStJeorEstimation($profile);
    }

    private function katchMcArdleEstimation(UserProfile $profile): array
    {
        $lbm = $profile->weight_kg * (1 - $profile->body_fat_pct / 100);

        $bmr  = 370 + (21.6 * $lbm);
        $tdee = $bmr * $this->activityMultiplier($profile->activity_level);

        $calories = (int) round($tdee + $this->goalAdjustment($profile->goal));

        // Protein based on LBM (2.2g per kg of lean mass)
        $protein = (int) round($lbm * 2.2);

        // Fill remaining calories: 60% carbs, 40% fat
        $remaining = $calories - ($protein * 4);
        $carbs = (int) round(($remaining * 0.60) / 4);
        $fat   = (int) round(($remaining * 0.40) / 9);

        return [
            'daily_calorie_target' => $calories,
            'daily_protein_g'      => $protein,
            'daily_carbs_g'        => $carbs,
            'daily_fat_g'          => $fat,
        ];
    }

    private function mifflinStJeorEstimation(UserProfile $profile): array
    {
        $age = $profile->date_of_birth->age;

        $bmr = (10 * $profile->weight_kg)
            + (6.25 * $profile->height_cm)
            - (5 * $age)
            + ($profile->gender === UserGender::MALE ? 5 : -161);

        $tdee = $bmr * $this->activityMultiplier($profile->activity_level);

        $calories = (int) round($tdee + $this->goalAdjustment($profile->goal));

        return [
            'daily_calorie_target' => $calories,
            'daily_protein_g'      => (int) round(($calories * 0.30) / 4),
            'daily_carbs_g'        => (int) round(($calories * 0.45) / 4),
            'daily_fat_g'          => (int) round(($calories * 0.25) / 9),
        ];
    }

    private function activityMultiplier(UserActivityLevels $level): float
    {
        return match ($level) {
            UserActivityLevels::SEDENTARY   => 1.2,
            UserActivityLevels::LIGHT       => 1.375,
            UserActivityLevels::MODERATE    => 1.55,
            UserActivityLevels::ACTIVE      => 1.725,
            UserActivityLevels::VERY_ACTIVE => 1.9,
        };
    }

    private function goalAdjustment(UserGoal $goal): int
    {
        return match ($goal) {
            UserGoal::LOSE_WEIGHT => -500,
            UserGoal::MAINTAIN    => 0,
            UserGoal::GAIN_MUSCLE => 300,
        };
    }
}
