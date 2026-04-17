<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\MealMacro;
use App\Models\MealPost;
use App\Models\MealPostIngredient;
use App\Models\UserProfile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateMealService
{
    private ?Collection $cachedIngredients = null;
    private const UNIT_MAP = [
        'grams'       => 'g',
        'gram'        => 'g',
        'gr'          => 'g',
        'grm'         => 'g',
        'milliliter'  => 'ml',
        'millilitre'  => 'ml',
        'ml'          => 'ml',
        'tablespoon'  => 'tbsp',
        'tablespoons' => 'tbsp',
        'tbs'         => 'tbsp',
        'teaspoon'    => 'tsp',
        'teaspoons'   => 'tsp',
        'kilogram'    => 'kg',
        'kilograms'   => 'kg',
        'kilo'        => 'kg',
        'liter'       => 'l',
        'litre'       => 'l',
        'liters'      => 'l',
        'piece'       => 'piece',
        'pieces'      => 'piece',
        'pcs'         => 'piece',
        'pc'          => 'piece',
        'pce'         => 'piece',
        'cup'         => 'cup',
        'cups'        => 'cup',
        'ounce'       => 'oz',
        'ounces'      => 'oz',
        'oz'          => 'oz',
        'pound'       => 'lb',
        'pounds'      => 'lb',
        'lb'          => 'lb',
        'lbs'         => 'lb',
    ];

    private $openAiService;

    public function __construct(private OpenAiService $openAi)
    {
        $this->openAiService = $openAi;
    }

    public function create(UserProfile $profile, array $validated, UploadedFile $image): array
    {
        foreach ($validated['ingredients'] as &$ingredient) {
            $ingredient['unit'] = $this->normalizeUnit($ingredient['unit']);
        }
        unset($ingredient);

        $normalizedIngredients = $this->normalizeIngredients($validated['ingredients']);

        DB::transaction(function () use ($profile, $validated, $normalizedIngredients, $image) {

            $imageData = $this->uploadImage($image);

            $resolvedIngredients = $this->resolveIngredients($normalizedIngredients);
            $mealFingerPrint = $this->generateMealFingerprint($resolvedIngredients);
            $macrosAndCalories = $this->calculateMacrosAndCalories($resolvedIngredients, $mealFingerPrint);

            $mealPost = $this->persistMeal($profile, $validated, $resolvedIngredients, $macrosAndCalories, $imageData);
        });

        return [
            'status' => 'success',
            'message' => 'Meal created successfully',
        ];
    }

    //create meal post and link to user profile
    private function persistMeal(mixed $profile, array $validated, array $resolvedIngredients, MealMacro $macros, array $imageData): MealPost
    {
        $mealPost = MealPost::create([
            'user_profile_id' => $profile->id,
            'fingerprint'     => $macros->fingerprint,
            'name'            => $validated['name'],
            'description'     => $validated['description'] ?? null,
            'visibility'      => $validated['visibility'],
            'image_url'       => $imageData['photo_url'],
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

        return $mealPost->load(['ingredients', 'mealMacro']);
    }

    private function uploadImage(UploadedFile $image): array
    {
        $path = $image->store('meals', 'public');
        $url  = Storage::disk('public')->url($path);

        return ['photo_path' => $path, 'photo_url' => $url];
    }

    //calculate calories and macros logic
    private function calculateMacrosAndCalories(array $resolvedIngredients, string $mealFingerPrint): MealMacro
    {
        $cachedMeal = MealMacro::where('fingerprint', $mealFingerPrint)->first();

        if ($cachedMeal) {
            return $cachedMeal;
        }

        $ingredientList = implode("\n", array_map(
            fn($item) => "{$item['ingredient']->name_en}: {$item['portion']} {$item['unit']}",
            $resolvedIngredients
        ));

        $data = $this->openAi->calculateMacros($ingredientList) ?? $this->openAi->calculateMacros($ingredientList);


        if ($data === null) {
            throw new Exception(
                'Could not calculate nutrition data. Please try again.'
            );
        }

        return MealMacro::create([
            'fingerprint' => $mealFingerPrint,
            'calories'    => $data['calories'],
            'protein'     => $data['protein'],
            'carbs'       => $data['carbs'],
            'fats'        => $data['fats'],
            'fiber'       => $data['fiber'],
        ]);
    }

    // Core logic to resolve ingredients
    private function resolveIngredients(array $normalizedIngredients): array
    {
        $resolved   = [];
        $unresolved = [];

        foreach ($normalizedIngredients as $item) {
            $match = $this->fuzzyCheckExistingIngredients($item['name']);
            if ($match) {
                $resolved[] = [
                    'input'      => $item['name'],
                    'ingredient' => $match,
                    'portion'    => $item['portion'],
                    'unit'       => $item['unit'],
                ];
            } else {
                $unresolved[] = $item;
            }
        }

        if (!empty($unresolved)) {
            $resolved = array_merge($resolved, $this->resolveIngrediantsViaOpenAI($unresolved));
        }

        return $resolved;
    }

    private function resolveIngrediantsViaOpenAI(array $unresolved): array
    {
        $names = array_column($unresolved, 'name');
        $items = $this->openAi->resolveIngredientNames($names);


        $unresolvedByName = array_column($unresolved, null, 'name');

        $resolved = [];

        foreach ($items as $item) {
            $original = $unresolvedByName[$item['input']] ?? null;

            $match = $this->fuzzyCheckExistingIngredients($item['name_en']);

            if (!$match) {
                $match = Ingredient::create([
                    'name_en'  => $item['name_en'],
                    'name_ar'  => data_get($item, 'name_ar'),
                    'source'   => 'user',
                    'verified' => false,
                ]);
            }

            $resolved[] = [
                'input'      => $item['input'],
                'ingredient' => $match,
                'portion'    => data_get($original, 'portion', 0),
                'unit'       => data_get($original, 'unit', 'g'),
            ];
        }

        return $resolved;
    }

    //normalization and fuzzy matching logic
    private function normalizeUnit(string $unit): string
    {
        $unit = strtolower(trim($unit));

        return self::UNIT_MAP[$unit] ?? $unit;
    }

    private function normalizeIngredients(array $ingredients): array
    {
        foreach ($ingredients as &$ingredient) {
            $name = strtolower(trim($ingredient['name']));
            $name = preg_replace('/\s+/', ' ', $name);
            $name = preg_replace('/[^\p{L}\p{Arabic} ]/u', '', $name);
            $ingredient['name'] = $name;
        }
        unset($ingredient);

        usort($ingredients, fn($a, $b) => strcmp($a['name'], $b['name']));

        return $ingredients;
    }

    private function fuzzyCheckExistingIngredients(string $input): ?Ingredient
    {
        if ($this->cachedIngredients === null) {
            $this->cachedIngredients = Ingredient::all();
        }

        $bestScore      = 0;
        $bestIngredient = null;

        foreach ($this->cachedIngredients as $ingredient) {
            similar_text($input, $ingredient->name_en, $percentEn);
            similar_text($input, $ingredient->name_ar ?? '', $percentAr);

            $score = max($percentEn, $percentAr);

            if ($score > $bestScore) {
                $bestScore      = $score;
                $bestIngredient = $ingredient;
            }
        }
        return $bestScore >= 85 ? $bestIngredient : null;
    }

    // Core logic to generate meal fingerprint
    private function generateMealFingerprint(array $resolvedIngredients): string
    {
        $items = array_map(fn($item) => [
            'id'      => $item['ingredient']->id,
            'portion' => $item['portion'],
            'unit'    => $item['unit'],
        ], $resolvedIngredients);


        usort($items, fn($a, $b) => $a['id'] <=> $b['id']);

        $string = implode('|', array_map(
            fn($item) => "{$item['id']}:{$item['portion']}:{$item['unit']}",
            $items
        ));

        return hash('sha256', $string);
    }
}
