<?php

namespace App\Services;

use Exception;
use OpenAI\Exceptions\TransporterException;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiService
{
    private $resolveIngredientPrompt = <<<PROMPT
        You are a culinary ingredient identifier.
        For each ingredient name provided return the standard
        English culinary name and its Arabic equivalent.
        Respond ONLY with a valid JSON array.
        No explanation. No markdown. No extra text.
        Format:
        [{ "input": "...", "name_en": "...", "name_ar": "..." }]
        Ingredients: %s
        PROMPT;

    private $estimateMealPrompt = <<<PROMPT
        You are a professional nutritionist with deep expertise in regional and international cuisines.
        Estimate the macronutrients for a TYPICAL SINGLE SERVING of the meal described below.
        Use the country/region to interpret traditional preparation methods, common ingredients, and standard portion sizes.
        If a description is provided, use it to refine your estimate as much as possible.

        Meal Name: %s
        Country/Region: %s
        Description: %s

        Respond ONLY with valid JSON. No explanation. No markdown.
        {
            "calories": 0,
            "protein": 0,
            "carbs": 0,
            "fats": 0,
            "fiber": 0
        }
        All values must be positive numbers rounded to 2 decimal places.
        PROMPT;

    private $calculateMacrosPrompt = <<<PROMPT
        You are a nutrition calculator.
        Calculate the total nutritional values for this entire meal.
        Respond ONLY with valid JSON. No explanation. No markdown.
        Format:
        {
        "calories": 0,
        "protein": 0,
        "carbs": 0,
        "fats": 0,
        "fiber": 0
        }
        All values must be numbers rounded to 2 decimal places.
        Ingredients: %s
        PROMPT;

    public function resolveIngredientNames(array $names): array
    {
        $nameList = implode(', ', $names);

        $prompt = sprintf($this->resolveIngredientPrompt, $nameList);

        try {
            $response = OpenAI::chat()->create([
                'model'       => env('OPENAI_MODEL_STAGE1'),
                'temperature' => 0,
                'max_tokens'  => 1000,
                'messages'    => [['role' => "user", 'content' => $prompt]],
            ]);
            $items = json_decode($this->stripMarkdown($response->choices[0]->message->content), true);

            if (!is_array($items)) {
                throw new Exception('Could not resolve one or more ingredients. Please check your input.');
            }

            return $items;
        } catch (TransporterException) {
            throw new Exception('Nutrition service is temporarily unavailable. Please try again later.');
        }
    }


    public function estimateMealMacros(string $name, ?string $description, string $country): ?array
    {
        $prompt = sprintf($this->estimateMealPrompt, $name, $country, $description ?? 'No description provided.');

        try {
            $response = OpenAI::chat()->create([
                'model'       => env('OPENAI_MODEL_STAGE2'),
                'temperature' => 0,
                'max_tokens'  => 300,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
            ]);

            $data = json_decode($this->stripMarkdown($response->choices[0]->message->content), true);

            return $this->isMacroValid($data) ? $data : null;
        } catch (TransporterException) {
            throw new Exception('Nutrition service is temporarily unavailable. Please try again later.');
        }
    }

    public function calculateMacros(string $ingredientList): ?array
    {
        $prompt = sprintf($this->calculateMacrosPrompt, $ingredientList);

        try {
            $response = OpenAI::chat()->create([
                'model'       => env('OPENAI_MODEL_STAGE2'),
                'temperature' => 0,
                'max_tokens'  => 300,
                'messages'    => [['role' => "user", 'content' => $prompt]],
            ]);
            $data = json_decode($this->stripMarkdown($response->choices[0]->message->content), true);

            return $this->isMacroValid($data) ? $data : null;
        } catch (TransporterException) {
            throw new Exception('Nutrition service is temporarily unavailable. Please try again later.');
        }
    }

    private function stripMarkdown(string $content): string
    {
        return preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/m', '$1', trim($content));
    }

    private function isMacroValid(?array $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach (['calories', 'protein', 'carbs', 'fats', 'fiber'] as $field) {
            if (!array_key_exists($field, $data) || !is_numeric($data[$field])) {
                return false;
            }
        }

        return $data['calories'] >= 0
            && $data['calories'] <= 15000
            && $data['protein']  >= 0
            && $data['carbs']    >= 0
            && $data['fats']     >= 0
            && $data['fiber']    >= 0;
    }
}
