<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecetteRequest;
use App\Http\Requests\UpdateRecetteRequest;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recette;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecipeWebController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $recipes = Recette::query()
            ->visibleTo($request->user())
            ->with(['user', 'categories'])
            ->latest()
            ->paginate();

        return view('recipes.index', compact('recipes'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $ingredients = Ingredient::all();

        return view('recipes.create', compact('categories', 'ingredients'));
    }

    public function store(StoreRecetteRequest $request): RedirectResponse
    {
        $recipe = DB::transaction(function () use ($request) {
            $attributes = $request->validated();
            unset($attributes['etapes'], $attributes['ingredients'], $attributes['categories']);

            $attributes['user_id'] = $request->user()->id;
            $attributes['is_ai_generated'] = false;

            $recipe = Recette::query()->create($attributes);
            $this->syncRelations($recipe, $request->validated());

            return $recipe;
        });

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Recipe created successfully.');
    }

    public function show(Request $request, Recette $recipe): View
    {
        $recipe = Recette::query()
            ->visibleTo($request->user())
            ->with(['user', 'etapes', 'ingredients', 'categories'])
            ->findOrFail($recipe->id);

        $favorite = $request->user()->favoris()->where('recette_id', $recipe->id)->first();

        return view('recipes.show', compact('recipe', 'favorite'));
    }

    public function edit(Recette $recipe): View
    {
        $this->authorize('update', $recipe);

        $recipe->load(['etapes', 'ingredients', 'categories']);
        $categories = Category::all();
        $ingredients = Ingredient::all();

        return view('recipes.edit', compact('recipe', 'categories', 'ingredients'));
    }

    public function update(UpdateRecetteRequest $request, Recette $recipe): RedirectResponse
    {
        $this->authorize('update', $recipe);

        DB::transaction(function () use ($request, $recipe) {
            $attributes = $request->validated();
            unset($attributes['etapes'], $attributes['ingredients'], $attributes['categories']);

            $recipe->update($attributes);
            $this->syncRelations($recipe, $request->validated());
        });

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recette $recipe): RedirectResponse
    {
        $this->authorize('delete', $recipe);

        $recipe->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe deleted successfully.');
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
