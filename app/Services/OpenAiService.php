<?php

namespace App\Services;

use Exception;
use OpenAI\Exceptions\TransporterException;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiService
{
    private string $resolveIngredientPrompt = <<<PROMPT
You are a culinary ingredient identifier.
For each ingredient name provided return the standard
English culinary name and its Arabic equivalent.
Respond ONLY with a valid JSON array.
No explanation. No markdown. No extra text.
If an ingredient cannot be identified return the closest 
possible culinary match. Never return empty fields.
name_en must be in title case. Example: "Olive Oil" not "olive oil".
name_ar must always be written in Arabic script. Never romanize.
Format:
[{ "input": "...", "name_en": "...", "name_ar": "..." }]
Ingredients: %s
PROMPT;

    private string $estimateMealPrompt = <<<PROMPT
You are an expert clinical nutritionist with deep knowledge 
of Middle Eastern, Mediterranean, and international cuisines.
A user wants to log a meal they consumed. Estimate the 
nutritional values for ONE standard serving of this meal.
Meal information:
- Meal name: %s
- User country: %s
- Additional context: %s
Guidelines:
1. Use the meal name and regional context to identify the 
   most likely traditional preparation for this specific area.
2. Account for local variations — the same dish prepared in 
   Lebanon differs from Egypt or Saudi Arabia in ingredients, 
   cooking method, and portion size.
3. If additional context is provided use it to refine the 
   estimate — street vendor implies different preparation 
   than homemade or restaurant.
4. Base your estimate on a realistic single serving size for 
   this dish in this region.
5. Account for cooking method impact — fried, grilled, baked,
   and boiled versions have meaningfully different macro profiles.
6. If the meal name is a recent or trending dish search for
   its most common preparation and ingredients before estimating.
Respond ONLY with valid JSON. No explanation. No markdown.
No text outside the JSON.
{
  "calories": 0,
  "protein": 0,
  "carbs": 0,
  "fats": 0,
  "fiber": 0
}
All values must be numbers rounded to 2 decimal places.
All values must be non-negative.
Calories must be between 1 and 5000 for a single serving.
PROMPT;

    private string $calculateMacrosPrompt = <<<PROMPT
You are a nutrition calculator.
Calculate the total nutritional values for this entire meal 
based on the ingredients and portions provided.
Respond ONLY with valid JSON. No explanation. No markdown. 
No text outside the JSON.
Format:
{
  "calories": 0,
  "protein": 0,
  "carbs": 0,
  "fats": 0,
  "fiber": 0
}
calories in kcal · protein, carbs, fats, fiber in grams.
All values must be numbers rounded to 2 decimal places.
All values must be non-negative.
Calories must be between 50 and 15000 for a full meal.
Ingredients:
%s
PROMPT;

    private string $healthCheckPrompt = <<<PROMPT
You are a clinical nutrition safety assistant.

A user with the following health conditions is about to 
consume or create a meal. Analyze whether any ingredients 
may negatively interact with their conditions.

User health conditions:
%s

Meal ingredients:
%s

Rules:
1. Only flag ingredients that pose a genuine, clinically
   significant risk for the specific condition listed.
2. Before flagging, calculate the actual content from the
   given portion and compare it to these minimum thresholds —
   only flag if the threshold is exceeded:
   - Hypertension: sodium > 600mg (salt > 1.5g per serving)
   - Diabetes: added sugar > 10g per serving
   - Heart disease: saturated fat > 5g per serving
   - Kidney disease: potassium > 500mg per serving
   - Celiac / gluten intolerance: any amount of gluten
   - Allergies: any amount of the allergen
3. Be specific — name the exact ingredient, the calculated
   amount, and why it exceeds the threshold for the condition.
4. If no threshold is exceeded return is_flagged as false
   with an empty flagged_ingredients array.

Respond ONLY with valid JSON. No explanation. No markdown.
No text outside the JSON.
{
  "is_flagged": false,
  "flagged_ingredients": [
    {
      "ingredient": "ingredient name",
      "concern": "specific reason this affects the condition",
      "condition": "the specific condition it affects",
      "severity": "high or medium or low"
    }
  ]
}
PROMPT;

    public function checkHealth(string $conditions, string $mealInfo): ?array
    {
        $prompt = sprintf($this->healthCheckPrompt, $conditions, $mealInfo);

        try {
            $response = $this->withRetry(fn() => OpenAI::chat()->create([
                'model'                 => env('OPENAI_MODEL_STAGE1'),
                'temperature'           => 0,
                'max_completion_tokens' => 800,
                'messages'              => [['role' => 'user', 'content' => $prompt]],
            ]));

            $data = json_decode(
                $this->stripMarkdown($response->choices[0]->message->content),
                true
            );

            return is_array($data) ? $data : null;
        } catch (TransporterException) {
            return null;
        }
    }

    public function resolveIngredientNames(array $names): array
    {
        $nameList = implode(', ', $names);
        $prompt   = sprintf($this->resolveIngredientPrompt, $nameList);

        try {
            $response = $this->withRetry(fn() => OpenAI::chat()->create([
                'model'                 => env('OPENAI_MODEL_STAGE1'),
                'temperature'           => 0,
                'max_completion_tokens' => 1000,
                'messages'              => [['role' => 'user', 'content' => $prompt]],
            ]));

            $items = json_decode(
                $this->stripMarkdown($response->choices[0]->message->content),
                true
            );

            if (!is_array($items)) {
                throw new Exception('Could not resolve one or more ingredients. Please check your input.');
            }

            return $items;
        } catch (TransporterException) {
            throw new Exception('Nutrition service is temporarily unavailable. Please try again later.');
        }
    }

    public function estimateMealMacros(string $name, ?string $description, string $country): array
    {
        $prompt = sprintf(
            $this->estimateMealPrompt,
            $name,
            $country,
            $description ?? 'No description provided.'
        );

        try {
            $response = $this->withRetry(fn() => OpenAI::chat()->create([
                'model'                 => env('OPENAI_ESTIMATION_MODEL'),
                'max_completion_tokens' => 500,
                'messages'              => [['role' => 'user', 'content' => $prompt]],
            ]));

            $data = json_decode(
                $this->stripMarkdown($response->choices[0]->message->content),
                true
            );

            if (!$this->isMacroValid($data, 5000)) {
                throw new Exception('Could not estimate macros for this meal. Please try again.');
            }

            return $data;
        } catch (TransporterException) {
            throw new Exception('Nutrition service is temporarily unavailable. Please try again later.');
        }
    }

    public function calculateMacros(string $ingredientList): array
    {
        $prompt = sprintf($this->calculateMacrosPrompt, $ingredientList);

        try {
            $response = $this->withRetry(fn() => OpenAI::chat()->create([
                'model'                 => env('OPENAI_MODEL_STAGE2'),
                'temperature'           => 0,
                'max_completion_tokens' => 300,
                'messages'              => [['role' => 'user', 'content' => $prompt]],
            ]));

            $data = json_decode(
                $this->stripMarkdown($response->choices[0]->message->content),
                true
            );

            if (!$this->isMacroValid($data, 15000)) {
                throw new Exception('Could not calculate nutrition data. Please try again.');
            }

            return $data;
        } catch (TransporterException) {
            throw new Exception('Nutrition service is temporarily unavailable. Please try again later.');
        }
    }

    private function withRetry(callable $callback, int $maxAttempts = 3): mixed
    {
        $attempt = 0;

        while (true) {
            try {
                return $callback();
            } catch (TransporterException $e) {
                $attempt++;

                if ($attempt >= $maxAttempts) {
                    throw $e;
                }

                // exponential backoff: 500ms, 1000ms
                usleep(500_000 * (2 ** ($attempt - 1)));
            }
        }
    }

    private function stripMarkdown(string $content): string
    {
        $stripped = preg_replace(
            '/^```(?:json)?\s*([\s\S]*?)\s*```$/m',
            '$1',
            trim($content)
        );

        if (preg_match('/(\{[\s\S]*\}|\[[\s\S]*\])/m', $stripped, $matches)) {
            return $matches[1];
        }

        return $stripped;
    }

    private function isMacroValid(?array $data, int $maxCalories = 15000): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach (['calories', 'protein', 'carbs', 'fats', 'fiber'] as $field) {
            if (!array_key_exists($field, $data) || !is_numeric($data[$field])) {
                return false;
            }
        }

        return $data['calories'] >= 1
            && $data['calories'] <= $maxCalories
            && $data['protein']  >= 0
            && $data['carbs']    >= 0
            && $data['fats']     >= 0
            && $data['fiber']    >= 0;
    }
}
