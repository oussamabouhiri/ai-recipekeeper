<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenerationIaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recette = $this->isCompleted() ? $this->getRecette() : null;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'model_used' => $this->model_used,
            'tokens_used' => $this->tokens_used,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'recipe' => $this->when(
                $recette !== null,
                fn () => new RecetteResource($recette->load(['etapes', 'ingredients', 'categories']))
            ),
        ];
    }
}
