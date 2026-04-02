<?php

namespace App\Services;

use App\Enums\UserOnboardingSteps;
use Illuminate\Support\Facades\DB;

class UserProfileService
{
    public function completeBasicInfo($user, $profileData)
    {
        DB::transaction(function () use ($user, $profileData) {
            $user->profile()->update($profileData);
            $user->profile->save();

            $user->onboarding_step = UserOnboardingSteps::TARGETS;
            $user->save();
        });
        return $user->profile()->first();
    }



    //============== Helper Functions =============

    private function calculateDailyTargets($profile)
    {
        // Implement the logic to calculate daily calorie, protein, carbs, and fat targets
        // based on the user's profile information
        return [
            'daily_calorie_target' => 2000, // Example value
            'daily_protein_g' => 150,      // Example value
            'daily_carbs_g' => 250,        // Example value
            'daily_fat_g' => 70,           // Example value
        ];
    }
}
