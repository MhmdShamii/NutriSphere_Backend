<?php

namespace Database\Seeders;

use App\Enums\UserActivityLevels;
use App\Enums\UserGoal;
use App\Enums\UserOnboardingSteps;
use App\Enums\UserRole;
use App\Models\Country;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
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

        $this->command->info("Admin user ready: {$email}");
    }
}
