<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateRecipeJob;
use App\Models\GenerationIa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenerationWebController extends Controller
{
    public function create()
    {
        return view('generations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingredients' => 'required|array|min:1|max:20',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'nullable|string|max:50',
            'ingredients.*.unit' => 'nullable|string|max:50',
            'preferences' => 'nullable|string|max:1000',
            'constraints' => 'nullable|string|max:1000',
            'servings' => 'nullable|integer|min:1|max:100',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $generation = GenerationIa::create([
            'user_id' => Auth::id(),
            'prompt' => json_encode($request->only([
                'ingredients', 'preferences', 'constraints', 'servings', 'difficulty',
            ])),
            'status' => GenerationIa::STATUS_PENDING,
        ]);

        GenerateRecipeJob::dispatch($generation);

        return redirect()->route('generations.show', $generation)
            ->with('success', 'Recipe generation started! This may take a minute.');
    }

    public function show(GenerationIa $generation)
    {
        if (! Auth::user()->isAdmin() && Auth::id() !== $generation->user_id) {
            abort(404);
        }

        return view('generations.show', ['generation' => $generation]);
    }
}
