<?php

namespace App\Services;

use App\Models\HealthCondition;
use App\Models\User;
use App\Models\UserHealthCondition;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class HealthConditionService
{
    public function getAll(): Collection
    {
        return HealthCondition::all();
    }

    public function getUserConditions(User $user): Collection
    {
        return $user->healthConditions()->with('condition')->get();
    }

    public function addCondition(User $user, array $data): UserHealthCondition
    {
        $conditionId = $data['health_condition_id'] ?? null;
        $custom      = $data['custom_condition'] ?? null;

        if ($conditionId && $custom) {
            throw new UnprocessableEntityHttpException('Provide either a predefined condition or a custom one, not both.');
        }

        if (!$conditionId && !$custom) {
            throw new UnprocessableEntityHttpException('A predefined condition or a custom condition is required.');
        }

        if ($conditionId) {
            $exists = $user->healthConditions()->where('health_condition_id', $conditionId)->exists();
            if ($exists) {
                throw new UnprocessableEntityHttpException('This condition is already added.');
            }
        }

        return $user->healthConditions()->create([
            'health_condition_id' => $conditionId,
            'custom_condition'    => $custom,
        ]);
    }

    public function removeCondition(User $user, int $id): void
    {
        $condition = $user->healthConditions()->findOrFail($id);
        $condition->delete();
    }
}
