<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'recette_id' => $this->recette_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'recette' => RecetteResource::make($this->whenLoaded('recette')),
        ];
    }
}
