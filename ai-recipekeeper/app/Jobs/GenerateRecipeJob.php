<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\GenerationIa;
use App\Models\Ingredient;
use App\Models\Recette;
use App\Services\OpenRouterService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GenerateRecipeJob extends GenerationIaJob
{
    public function handle(): void
    {
        $this->generation->update([
            'status' => GenerationIa::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        $input = json_decode($this->generation->prompt, true);

        if (! is_array($input)) {
            throw new RuntimeException('Invalid generation input.');
        }

        $service = app(OpenRouterService::class);
        $result = $service->generateRecipe($input);

        $recipeData = $result['recipe'];

        $recette = DB::transaction(function () use ($recipeData, $input) {
            $recette = Recette::create([
                'title' => $recipeData['title'],
                'description' => $recipeData['description'] ?? null,
                'prep_time' => $recipeData['prep_time'] ?? null,
                'cook_time' => $recipeData['cook_time'] ?? null,
                'servings' => $recipeData['servings'] ?? null,
                'difficulty' => $recipeData['difficulty'] ?? null,
                'user_id' => $this->generation->user_id,
                'is_ai_generated' => true,
                'statut' => 'published',
            ]);

            $this->createEtapes($recette, $recipeData['etapes'] ?? []);
            $this->attachIngredients($recette, $recipeData['ingredients'] ?? []);
            $this->attachCategories($recette, $input['categories'] ?? []);

            return $recette;
        });

        $this->generation->update([
            'status' => GenerationIa::STATUS_COMPLETED,
            'completed_at' => now(),
            'response' => json_encode([
                'recipe_id' => $recette->id,
                'ai_data' => $recipeData,
            ]),
            'model_used' => $result['model_used'],
            'tokens_used' => $result['tokens_used'],
        ]);
    }

    private function createEtapes(Recette $recette, array $etapes): void
    {
        $etapeRecords = collect($etapes)
            ->sortBy('step_number')
            ->values()
            ->map(fn (array $etape) => [
                'step_number' => $etape['step_number'],
                'instruction' => $etape['instruction'],
            ])
            ->all();

        $recette->etapes()->createMany($etapeRecords);
    }

    private function attachIngredients(Recette $recette, array $ingredients): void
    {
        $pivotData = collect($ingredients)->mapWithKeys(function (array $ingredient) {
            $record = Ingredient::firstOrCreate(
                ['name' => mb_strtolower($ingredient['name'])],
                ['name' => $ingredient['name']]
            );

            return [$record->id => [
                'quantity' => $ingredient['quantity'] ?? null,
                'unit' => $ingredient['unit'] ?? null,
            ]];
        })->all();

        $recette->ingredients()->sync($pivotData);
    }

    private function attachCategories(Recette $recette, array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $existingIds = Category::whereIn('id', $categoryIds)->pluck('id')->all();

        $recette->categories()->sync($existingIds);
    }
}
