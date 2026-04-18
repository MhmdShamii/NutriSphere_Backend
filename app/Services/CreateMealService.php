<?php

namespace App\Services;

use App\Models\MealMacro;
use App\Models\MealPost;
use App\Models\MealPostIngredient;
use App\Models\MealPreparationStep;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class CreateMealService
{
    public function __construct(private CalculateMacrosService $macrosService) {}

    public function create(UserProfile $profile, array $validated): MealPost
    {
        return DB::transaction(function () use ($profile, $validated) {
            [$resolvedIngredients, $macros] = $this->macrosService->calculateMealMacrosPipeline($validated['ingredients']);

            return $this->persistMeal($profile, $validated, $resolvedIngredients, $macros);
        });
    }

    private function persistMeal(UserProfile $profile, array $validated, array $resolvedIngredients, MealMacro $macros): MealPost
    {
        $mealPost = MealPost::create([
            'user_profile_id' => $profile->id,
            'fingerprint'     => $macros->fingerprint,
            'name'            => $validated['name'],
            'description'     => $validated['description'] ?? null,
            'visibility'      => $validated['visibility'],
            'servings'        => $validated['servings'],
            'image_url'       => null,
            'confirmed_at'    => null,
        ]);

        foreach ($resolvedIngredients as $item) {
            MealPostIngredient::create([
                'meal_post_id'  => $mealPost->id,
                'ingredient_id' => $item['ingredient']->id,
                'portion'       => $item['portion'],
                'unit'          => $item['unit'],
            ]);
        }

        foreach ($validated['preparation_steps'] ?? [] as $index => $step) {
            MealPreparationStep::create([
                'meal_post_id' => $mealPost->id,
                'step_number'  => $index + 1,
                'description'  => $step['description'],
            ]);
        }

        $mealPost->load(['ingredients', 'mealMacro', 'preparationSteps']);

        return $mealPost;
    }
}
