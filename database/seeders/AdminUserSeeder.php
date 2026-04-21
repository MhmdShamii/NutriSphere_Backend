<?php

namespace Database\Seeders;

use App\Enums\UserOnboardingSteps;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'unknown@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        User::firstOrCreate(
            ['email' => $email],
            [
                'first_name'        => 'Admin',
                'last_name'         => 'NutriSphere',
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'role'              => UserRole::ADMIN,
                'onboarding_step'   => UserOnboardingSteps::COMPLETE,
            ]
        );

        $this->command->info("Admin user ready: {$email}");
    }
}
