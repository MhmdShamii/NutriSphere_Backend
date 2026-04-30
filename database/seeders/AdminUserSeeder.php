<?php

namespace Database\Seeders;

use App\Enums\UserActivityLevels;
use App\Enums\UserGoal;
use App\Enums\UserOnboardingSteps;
use App\Enums\UserRole;
use App\Models\Country;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserWeightLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'unknown@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        $lebanon = Country::where('name', 'Lebanon')->first();

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'first_name'        => 'Admin',
                'last_name'         => 'NutriSphere',
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'role'              => UserRole::ADMIN,
                'onboarding_step'   => UserOnboardingSteps::COMPLETE,
                'country_id'        => $lebanon?->id,
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth'        => '1990-01-01',
                'gender'               => 'male',
                'weight_kg'            => 75.0,
                'height_cm'            => 175.0,
                'activity_level'       => UserActivityLevels::MODERATE,
                'goal'                 => UserGoal::MAINTAIN,
                'daily_calorie_target' => 2200,
                'daily_protein_g'      => 140,
                'daily_carbs_g'        => 240,
                'daily_fat_g'          => 70,
            ]
        );

        $weights = [75.8, 75.5, 75.3, 74.9, 74.7, 74.5, 74.3];

        foreach ($weights as $i => $weight) {
            $date = Carbon::today()->subDays(6 - $i)->toDateString();
            UserWeightLog::updateOrCreate(
                ['user_id' => $user->id, 'logged_at' => $date],
                ['weight_kg' => $weight]
            );
        }

        // Backdate so analytics history doesn't get blocked by the profile creation check
        User::where('id', $user->id)->update(['created_at' => Carbon::today()->subDays(40)]);
        UserProfile::where('user_id', $user->id)->update(['created_at' => Carbon::today()->subDays(40)]);

        $this->command->info("Admin user ready: {$email}");
    }
}
