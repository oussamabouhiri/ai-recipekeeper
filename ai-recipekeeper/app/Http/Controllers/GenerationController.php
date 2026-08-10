<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenerationRequest;
use App\Http\Resources\GenerationIaResource;
use App\Jobs\GenerateRecipeJob;
use App\Models\GenerationIa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GenerationController extends Controller
{
    public function store(StoreGenerationRequest $request): JsonResponse
    {
        $generation = GenerationIa::create([
            'user_id' => $request->user()->id,
            'prompt' => json_encode($request->validated()),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        $job = GenerateRecipeJob::dispatch($generation);

        return (new GenerationIaResource($generation))
            ->response()
            ->setStatusCode(202);
    }

    public function show(Request $request, GenerationIa $generation): GenerationIaResource
    {
        if (! $request->user()->isAdmin() && $request->user()->id !== $generation->user_id) {
            abort(404);
        }

        return new GenerationIaResource($generation);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = GenerationIa::query()
            ->where('user_id', $request->user()->id)
            ->latest();

        if ($request->user()->isAdmin()) {
            $query = GenerationIa::query()->latest();
        }

        return GenerationIaResource::collection(
            $query->paginate()
        );
    }
}
