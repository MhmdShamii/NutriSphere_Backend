<?php

namespace App\Services;

use Exception;
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

    private function callOpenAi($model, $maxTokens, $roll, $temparature = 0, $message)
    {
        return OpenAI::chat()->create([
            'model'       => $model,
            'temperature' => $temparature,
            'max_tokens'  => $maxTokens,
            'messages'    => [['role' => $roll, 'content' => $message]],
        ]);
    }

    public function resolveIngredientNames(array $names): array
    {
        $nameList = implode(', ', $names);

        $prompt = sprintf($this->resolveIngredientPrompt, $nameList);

        try {
            $response = $this->callOpenAi(env('OPENAI_MODEL_STAGE1'), 1000, "user", message: $prompt);
            $items = json_decode($response->choices[0]->message->content, true);

            if (!is_array($items)) {
                throw new \RuntimeException('Malformed JSON');
            }

            return $items;
        } catch (\Throwable) {
            throw new Exception(
                'Could not resolve one or more ingredients. Please check your input.'
            );
        }
    }


    public function calculateMacros(string $ingredientList): ?array
    {
        $prompt = sprintf($this->calculateMacrosPrompt, $ingredientList);

        try {
            $response = $this->callOpenAi(env('OPENAI_MODEL_STAGE2'), 300, "user", message: $prompt);
            $data = json_decode($response->choices[0]->message->content, true);

            return $this->isMacroValid($data) ? $data : null;
        } catch (\Throwable) {
            throw new Exception(
                'Could not calculate macros. Please check your input.'
            );
        }
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
