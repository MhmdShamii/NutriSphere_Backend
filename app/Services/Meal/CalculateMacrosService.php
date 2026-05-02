<?php

namespace App\Services\Meal;

use App\Enums\IngredientSource;
use App\Exceptions\AiServiceException;
use App\Services\OpenAiService;
use App\Models\Ingredient;
use App\Models\MealMacro;

class CalculateMacrosService
{
    private const FUZZY_THRESHOLD  = 85;
    private const FUZZY_CANDIDATES = 20;

    private const UNIT_MAP = [
        // weight
        'g'           => 'g',
        'gram'        => 'g',
        'grams'       => 'g',
        'gr'          => 'g',
        'grm'         => 'g',
        'kg'          => 'kg',
        'kilogram'    => 'kg',
        'kilograms'   => 'kg',
        'kilo'        => 'kg',
        'oz'          => 'oz',
        'ounce'       => 'oz',
        'ounces'      => 'oz',
        'lb'          => 'lb',
        'lbs'         => 'lb',
        'pound'       => 'lb',
        'pounds'      => 'lb',

        // volume
        'ml'          => 'ml',
        'milliliter'  => 'ml',
        'millilitre'  => 'ml',
        'milliliters' => 'ml',
        'millilitres' => 'ml',
        'l'           => 'l',
        'liter'       => 'l',
        'litre'       => 'l',
        'liters'      => 'l',
        'litres'      => 'l',
        'cup'         => 'cup',
        'cups'        => 'cup',
        'tbsp'        => 'tbsp',
        'tablespoon'  => 'tbsp',
        'tablespoons' => 'tbsp',
        'tbs'         => 'tbsp',
        'tsp'         => 'tsp',
        'teaspoon'    => 'tsp',
        'teaspoons'   => 'tsp',

        // count / descriptive
        'piece'       => 'piece',
        'pieces'      => 'piece',
        'pcs'         => 'piece',
        'pc'          => 'piece',
        'pce'         => 'piece',
        'serving'     => 'serving',
        'servings'    => 'serving',
        'slice'       => 'slice',
        'slices'      => 'slice',
        'pinch'       => 'pinch',
        'pinches'     => 'pinch',
        'clove'       => 'clove',
        'cloves'      => 'clove',
    ];

    public function __construct(private OpenAiService $openAi) {}

    public function estimateMacrosPipeline(string $name, ?string $description, string $country): MealMacro
    {
        $data = $this->openAi->estimateMealMacros($name, $description, $country);

        if ($data === null) {
            throw new AiServiceException('Could not estimate nutrition data. Please try again.');
        }

        return new MealMacro([
            'calories' => $data['calories'],
            'protein'  => $data['protein'],
            'carbs'    => $data['carbs'],
            'fats'     => $data['fats'],
            'fiber'    => $data['fiber'],
        ]);
    }

    public function calculateMealMacrosPipeline(array $ingredients): array
    {
        foreach ($ingredients as &$ingredient) {
            $ingredient['unit'] = $this->normalizeUnit($ingredient['unit']);
        }
        unset($ingredient);

        $normalized          = $this->normalizeIngredients($ingredients);
        $resolvedIngredients = $this->resolveIngredients($normalized);
        $fingerprint         = $this->generateMealFingerprint($resolvedIngredients);
        $macros              = $this->calculateMacrosAndCalories($resolvedIngredients, $fingerprint);

        return [$resolvedIngredients, $macros];
    }

    private function calculateMacrosAndCalories(array $resolvedIngredients, string $fingerprint): MealMacro
    {
        $cached = MealMacro::where('fingerprint', $fingerprint)->first();

        if ($cached) {
            return $cached;
        }

        $ingredientList = implode("\n", array_map(
            fn($item) => "{$item['ingredient']->name_en}: {$item['portion']} {$item['unit']}",
            $resolvedIngredients
        ));

        $data = $this->openAi->calculateMacros($ingredientList);

        if ($data === null) {
            throw new AiServiceException('Could not calculate nutrition data. Please try again.');
        }

        return MealMacro::create([
            'fingerprint' => $fingerprint,
            'calories'    => $data['calories'],
            'protein'     => $data['protein'],
            'carbs'       => $data['carbs'],
            'fats'        => $data['fats'],
            'fiber'       => $data['fiber'],
        ]);
    }

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
            $resolved = array_merge($resolved, $this->resolveIngredientsViaOpenAI($unresolved));
        }

        return $resolved;
    }

    private function resolveIngredientsViaOpenAI(array $unresolved): array
    {
        $names            = array_column($unresolved, 'name');
        $items            = $this->openAi->resolveIngredientNames($names);
        $unresolvedByName = array_column($unresolved, null, 'name');
        $resolved         = [];

        foreach ($items as $item) {
            if (!is_array($item) || empty($item['input']) || empty($item['name_en'])) {
                continue;
            }

            $original = $unresolvedByName[$item['input']] ?? null;
            $nameEn   = ucwords(strtolower($item['name_en']));
            $match    = $this->fuzzyCheckExistingIngredients($nameEn);

            if (!$match) {
                $match = Ingredient::create([
                    'name_en'  => $nameEn,
                    'name_ar'  => data_get($item, 'name_ar'),
                    'source'   => IngredientSource::USER,
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

    private function normalizeUnit(string $unit): string
    {
        return self::UNIT_MAP[strtolower(trim($unit))] ?? strtolower(trim($unit));
    }

    private function normalizeIngredients(array $ingredients): array
    {
        foreach ($ingredients as &$ingredient) {
            $name = strtolower(trim($ingredient['name']));
            $name = preg_replace('/[^\p{L}\p{Arabic} ]/u', '', $name);
            $name = trim(preg_replace('/\s+/', ' ', $name));
            $ingredient['name'] = $name;
        }
        unset($ingredient);

        usort($ingredients, fn($a, $b) => strcmp($a['name'], $b['name']));

        return $ingredients;
    }

    private function fuzzyCheckExistingIngredients(string $input): ?Ingredient
    {
        $prefix = strtolower(mb_substr($input, 0, 3));

        $candidates = Ingredient::whereRaw('LOWER(name_en) LIKE ?', ["{$prefix}%"])
            ->orWhereRaw('LOWER(name_ar) LIKE ?', ["{$prefix}%"])
            ->limit(self::FUZZY_CANDIDATES)
            ->get();

        $bestScore      = 0;
        $bestIngredient = null;

        foreach ($candidates as $ingredient) {
            similar_text($input, strtolower($ingredient->name_en), $percentEn);

            $percentAr = 0.0;
            if (!empty($ingredient->name_ar)) {
                similar_text($input, $ingredient->name_ar, $percentAr);
            }

            $score = max($percentEn, $percentAr);

            if ($score > $bestScore) {
                $bestScore      = $score;
                $bestIngredient = $ingredient;
            }
        }

        return $bestScore >= self::FUZZY_THRESHOLD ? $bestIngredient : null;
    }

    private function generateMealFingerprint(array $resolvedIngredients): string
    {
        $items = array_map(fn($item) => [
            'id'      => $item['ingredient']->id,
            'portion' => $item['portion'],
            'unit'    => $item['unit'],
        ], $resolvedIngredients);

        usort($items, fn($a, $b) => $a['id'] <=> $b['id']);

        $string = implode('|', array_map(
            fn($item) => "{$item['id']}:" . number_format((float) $item['portion'], 2, '.', '') . ":{$item['unit']}",
            $items
        ));

        return hash('sha256', $string);
    }
}
