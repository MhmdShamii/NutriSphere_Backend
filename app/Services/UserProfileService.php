<?php

namespace App\Services;

use App\Enums\UserActivityLevels;
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
            $targets = $this->mifflinStJeorEstimation($profile);
            $user->profile()->update($targets);

            if ($user->onboarding_step === UserOnboardingSteps::BASIC_INFO) {
                $user->update(['onboarding_step' => UserOnboardingSteps::TARGETS]);
            }
        });

        return $user->profile()->first();
    }

    public function completeTargets($user, array $targets)
    {
        DB::transaction(function () use ($user, $targets) {
            $user->profile()->update($targets);

            if ($user->onboarding_step === UserOnboardingSteps::TARGETS) {
                $user->update(['onboarding_step' => UserOnboardingSteps::COMPLETE]);
            }
        });

        return $user->profile()->first();
    }

    //============== Helper Functions =============

    private function mifflinStJeorEstimation(UserProfile $profile): array
    {
        $age = $profile->date_of_birth->age;

        $bmr = (10 * $profile->weight_kg)
            + (6.25 * $profile->height_cm)
            - (5 * $age)
            + ($profile->gender === 'male' ? 5 : -161);

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
            UserActivityLevels::SEDENTARY  => 1.2,
            UserActivityLevels::LIGHT      => 1.375,
            UserActivityLevels::MODERATE   => 1.55,
            UserActivityLevels::ACTIVE     => 1.725,
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
