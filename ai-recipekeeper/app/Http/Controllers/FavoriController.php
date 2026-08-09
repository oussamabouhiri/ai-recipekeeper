<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Resources\FavoriResource;
use App\Models\Favori;
use App\Models\Recette;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        return FavoriResource::collection(
            $request->user()->favoris()
                ->with('recette')
                ->latest()
                ->paginate()
        );
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $recipe = Recette::query()
            ->visibleTo($request->user())
            ->findOrFail($request->validated('recette_id'));

        $favori = $request->user()->favoris()->create([
            'recette_id' => $recipe->id,
        ]);

        return (new FavoriResource($favori->load('recette')))->response()->setStatusCode(201);
    }

    public function destroy(Favori $favori): JsonResponse
    {
        $this->authorize('delete', $favori);

        $favori->delete();

        return response()->json(null, 204);
    }
}
