<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvisRequest;
use App\Http\Requests\UpdateAvisRequest;
use App\Http\Resources\AvisResource;
use App\Models\Avis;
use App\Models\Recette;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AvisController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Recette $recipe): AnonymousResourceCollection
    {
        $recipe = Recette::query()
            ->visibleTo($request->user())
            ->withAvg('avis as rating_avg', 'rating')
            ->withCount('avis as rating_count')
            ->findOrFail($recipe->id);

        $ratingAvg = $recipe->rating_avg === null ? null : round((float) $recipe->rating_avg, 1);

        return AvisResource::collection(
            $recipe->avis()
                ->with('user:id,name')
                ->latest()
                ->paginate()
        )->additional([
            'rating_avg' => $ratingAvg,
            'rating_count' => (int) $recipe->rating_count,
        ]);
    }

    public function store(StoreAvisRequest $request, Recette $recipe): JsonResponse
    {
        $recipe = Recette::query()
            ->visibleTo($request->user())
            ->findOrFail($recipe->id);

        $avis = $recipe->avis()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return (new AvisResource($avis->load('user')))->response()->setStatusCode(201);
    }

    public function update(UpdateAvisRequest $request, Avis $avis): AvisResource
    {
        $this->authorize('update', $avis);

        $avis->update($request->validated());

        return new AvisResource($avis->load('user'));
    }

    public function destroy(Avis $avis): JsonResponse
    {
        $this->authorize('delete', $avis);

        $avis->delete();

        return response()->json(null, 204);
    }
}
