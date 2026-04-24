<?php

namespace App\Services\Meal;

use App\Models\MealPost;
use App\Services\OpenAiService;
use App\Models\User;

class HealthWarningService
{
    public function __construct(private OpenAiService $openAi) {}

    public function fromMealPost(User $user, MealPost $meal): ?array
    {
        $conditions = $this->getConditionsString($user);
        if (!$conditions) {
            return null;
        }

        $meal->loadMissing('ingredients');

        $mealInfo = $meal->ingredients->map(
            fn($i) => "{$i->name_en}: {$i->pivot->portion} {$i->pivot->unit}"
        )->implode("\n");

        return $this->openAi->checkHealth($conditions, $mealInfo);
    }

    public function fromIngredients(User $user, array $ingredients): ?array
    {
        $conditions = $this->getConditionsString($user);
        if (!$conditions) {
            return null;
        }

        $mealInfo = implode("\n", array_map(
            fn($i) => "{$i['name']}: {$i['portion']} {$i['unit']}",
            $ingredients
        ));

        return $this->openAi->checkHealth($conditions, $mealInfo);
    }

    public function fromMealName(User $user, string $name, ?string $description): ?array
    {
        $conditions = $this->getConditionsString($user);
        if (!$conditions) {
            return null;
        }

        $mealInfo = "Meal name: {$name}" . ($description ? "\nDescription: {$description}" : '');

        return $this->openAi->checkHealth($conditions, $mealInfo);
    }

    private function getConditionsString(User $user): string
    {
        $userConditions = $user->healthConditions()->with('condition')->get();

        return $userConditions->map(function ($uc) {
            return $uc->condition ? $uc->condition->name : $uc->custom_condition;
        })->filter()->implode(', ');
    }
}
