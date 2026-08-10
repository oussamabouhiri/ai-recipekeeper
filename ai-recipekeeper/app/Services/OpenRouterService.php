<?php

namespace App\Services;

use function Laravel\Ai\agent;

class OpenRouterService
{
    public function generateRecipe(array $input): array
    {
        $schemaAgent = agent(
            instructions: $this->buildSystemPrompt(),
            schema: fn ($s) => [
                'title' => $s->string()->required(),
                'description' => $s->string()->required(),
                'prep_time' => $s->integer()->required(),
                'cook_time' => $s->integer()->required(),
                'servings' => $s->integer()->required(),
                'difficulty' => $s->string()->enum(['easy', 'medium', 'hard'])->required(),
                'ingredients' => $s->array()->items(
                    $s->object([
                        'name' => $s->string()->required(),
                        'quantity' => $s->string()->required(),
                        'unit' => $s->string()->required(),
                    ])
                )->required(),
                'categories' => $s->array()->items($s->string())->required(),
                'etapes' => $s->array()->items(
                    $s->object([
                        'step_number' => $s->integer()->required(),
                        'instruction' => $s->string()->required(),
                    ])
                )->required(),
            ],
        );

        $response = $schemaAgent->prompt(
            prompt: $this->buildUserMessage($input),
            provider: 'openrouter',
        );

        return [
            'recipe' => $response->toArray(),
            'model_used' => $response->meta->model,
            'tokens_used' => $response->usage->promptTokens + $response->usage->completionTokens,
        ];
    }

    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
You are a professional chef and recipe developer. Given a list of ingredients and any preferences or constraints, generate a complete, well-structured recipe.

Rules:
- prep_time and cook_time are in minutes (integers)
- difficulty must be one of: "easy", "medium", "hard"
- Each ingredient must have a name, quantity (string), and unit (string)
- Each etape must have a step_number (integer starting at 1) and instruction (string)
- Steps must be in logical cooking order
- Use the provided categories as context for the recipe
PROMPT;
    }

    private function buildUserMessage(array $input): string
    {
        $lines = ['Generate a recipe using these ingredients:'];

        foreach ($input['ingredients'] as $ingredient) {
            $line = '- '.$ingredient['name'];
            if (! empty($ingredient['quantity'])) {
                $line .= ': '.$ingredient['quantity'];
                if (! empty($ingredient['unit'])) {
                    $line .= ' '.$ingredient['unit'];
                }
            }
            $lines[] = $line;
        }

        if (! empty($input['preferences'])) {
            $lines[] = '';
            $lines[] = 'Preferences: '.$input['preferences'];
        }

        if (! empty($input['constraints'])) {
            $lines[] = '';
            $lines[] = 'Constraints: '.$input['constraints'];
        }

        if (! empty($input['servings'])) {
            $lines[] = '';
            $lines[] = 'Servings: '.$input['servings'];
        }

        if (! empty($input['difficulty'])) {
            $lines[] = '';
            $lines[] = 'Difficulty: '.$input['difficulty'];
        }

        return implode("\n", $lines);
    }
}
