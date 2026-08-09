<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecetteRequest;
use App\Http\Requests\UpdateRecetteRequest;
use App\Http\Resources\RecetteResource;
use App\Models\Recette;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RecetteController extends Controller
{
    use AuthorizesRequests;

    private const WITH = ['user', 'etapes', 'ingredients', 'categories'];

    public function index(Request $request): AnonymousResourceCollection
    {
        return RecetteResource::collection(
            Recette::query()
                ->visibleTo($request->user())
                ->with(self::WITH)
                ->latest()
                ->paginate()
        );
    }

    public function store(StoreRecetteRequest $request): JsonResponse
    {
        $recette = DB::transaction(function () use ($request) {
            $attributes = $request->validated();
            unset($attributes['etapes'], $attributes['ingredients'], $attributes['categories']);

            $attributes['user_id'] = $request->user()->id;
            $attributes['is_ai_generated'] = false;

            $recette = Recette::query()->create($attributes);
            $this->syncRelations($recette, $request->validated());

            return $recette->refresh()->load(self::WITH);
        });

        return (new RecetteResource($recette))->response()->setStatusCode(201);
    }

    public function show(Request $request, Recette $recipe): RecetteResource
    {
        return new RecetteResource(
            Recette::query()
                ->visibleTo($request->user())
                ->with(self::WITH)
                ->findOrFail($recipe->id)
        );
    }

    public function update(UpdateRecetteRequest $request, Recette $recipe): RecetteResource
    {
        $this->authorize('update', $recipe);

        DB::transaction(function () use ($request, $recipe) {
            $attributes = $request->validated();
            unset($attributes['etapes'], $attributes['ingredients'], $attributes['categories']);

            $recipe->update($attributes);
            $this->syncRelations($recipe, $request->validated());
        });

        return new RecetteResource($recipe->load(self::WITH));
    }

    public function destroy(Recette $recipe): JsonResponse
    {
        $this->authorize('delete', $recipe);

        $recipe->delete();

        return response()->json(null, 204);
    }

    private function syncRelations(Recette $recette, array $data): void
    {
        if (isset($data['etapes'])) {
            $recette->etapes()->delete();
            $recette->etapes()->createMany($data['etapes']);
        }

        if (isset($data['ingredients'])) {
            $ingredients = collect($data['ingredients'])->mapWithKeys(fn ($item) => [
                $item['ingredient_id'] => [
                    'quantity' => $item['quantity'] ?? null,
                    'unit' => $item['unit'] ?? null,
                ],
            ])->all();

            $recette->ingredients()->sync($ingredients);
        }

        if (isset($data['categories'])) {
            $recette->categories()->sync($data['categories']);
        }
    }
}
