<?php

namespace App\Services;

use App\Enums\UserOnboardingSteps;
use App\Models\HealthCondition;
use App\Models\User;
use App\Models\UserHealthCondition;
use Illuminate\Support\Collection;

class HealthConditionService
{
    public function getAll(): Collection
    {
        return HealthCondition::select('id', 'name', 'slug', 'type', 'severity')->get();
    }

    public function getUserConditions(User $user): Collection
    {
        return $user->healthConditions()->select('id', 'custom_condition', 'health_condition_id')->with('condition:id,name,slug,type,severity')->get();
    }

    public function addCondition(User $user, array $data): UserHealthCondition
    {
        return $user->healthConditions()->create([
            'health_condition_id' => data_get($data, 'health_condition_id'),
            'custom_condition'    => data_get($data, 'custom_condition'),
        ]);
    }

    public function completeHealthConditions(User $user): void
    {
        if ($user->onboarding_step === UserOnboardingSteps::HEALTH_CONDITIONS) {
            $user->onboarding_step = UserOnboardingSteps::COMPLETE;
            $user->save();
        }
    }

    public function removeCondition(User $user, int $id): void
    {
        $condition = $user->healthConditions()->findOrFail($id);
        $condition->delete();
    }
}
