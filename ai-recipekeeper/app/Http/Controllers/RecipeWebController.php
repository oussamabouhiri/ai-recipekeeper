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
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecipeWebController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $user = $request->user();

        $recipes = Recette::query()
            ->where('user_id', $user->id)
            ->with(['user', 'categories'])
            ->latest()
            ->paginate();

        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('recipes.index', compact('recipes', 'user', 'initials'));
    }

    public function browse(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');

        $recipes = Recette::query()
            ->visibleTo($request->user())
            ->with(['categories', 'favoris'])
            ->when($search, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%")))
            ->when($categoryId, fn ($q, $id) => $q->whereHas('categories', fn ($c) => $c->where('categories.id', $id)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $favoriteIds = $request->user()->favoris()->pluck('recette_id');
        $favoriteMap = $request->user()->favoris()->get()->mapWithKeys(fn ($f) => [$f->recette_id => $f]);

        $user = $request->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('recipes.browse', compact('recipes', 'categories', 'favoriteIds', 'favoriteMap', 'search', 'user', 'initials'));
    }

    public function create(Request $request): View
    {
        $categories = Category::all();
        $ingredients = Ingredient::all();

        $user = $request->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('recipes.create', compact('categories', 'ingredients', 'user', 'initials'));
    }

    public function store(StoreRecetteRequest $request): RedirectResponse
    {
        $recipe = DB::transaction(function () use ($request) {
            $attributes = $request->validated();
            unset($attributes['etapes'], $attributes['ingredients'], $attributes['categories']);

            if ($request->hasFile('image_path')) {
                $file = $request->file('image_path');
                $filename = Str::slug($request->input('title', 'recipe')) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/recipes'), $filename);
                $attributes['image_path'] = 'images/recipes/' . $filename;
            } else {
                unset($attributes['image_path']);
            }

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
            ->with(['user', 'etapes', 'ingredients', 'categories', 'avis.user:id,name'])
            ->findOrFail($recipe->id);

        $favorite = $request->user()->favoris()->where('recette_id', $recipe->id)->first();
        $userReview = $recipe->avis->firstWhere('user_id', $request->user()->id);

        $ratingAvg = $recipe->avis->avg('rating');
        $ratingAvg = $ratingAvg === null ? null : round((float) $ratingAvg, 1);
        $ratingCount = $recipe->avis->count();

        $user = $request->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('recipes.show', compact('recipe', 'favorite', 'userReview', 'ratingAvg', 'ratingCount', 'user', 'initials'));
    }

    public function edit(Request $request, Recette $recipe): View
    {
        $this->authorize('update', $recipe);

        $recipe->load(['etapes', 'ingredients', 'categories']);
        $categories = Category::all();
        $ingredients = Ingredient::all();

        $user = $request->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('recipes.edit', compact('recipe', 'categories', 'ingredients', 'user', 'initials'));
    }

    public function update(UpdateRecetteRequest $request, Recette $recipe): RedirectResponse
    {
        $this->authorize('update', $recipe);

        DB::transaction(function () use ($request, $recipe) {
            $attributes = $request->validated();
            unset($attributes['etapes'], $attributes['ingredients'], $attributes['categories']);

            if ($request->hasFile('image_path')) {
                $file = $request->file('image_path');
                $filename = Str::slug($request->input('title', 'recipe')) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/recipes'), $filename);
                $attributes['image_path'] = 'images/recipes/' . $filename;
            } elseif ($request->input('image_path_delete') === '1') {
                $attributes['image_path'] = null;
            } else {
                unset($attributes['image_path']);
            }

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
