<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteRequest;
use App\Models\Category;
use App\Models\Favori;
use App\Models\Recette;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriWebController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');

        $favoris = $request->user()->favoris()
            ->with('recette.user', 'recette.categories', 'recette.avis')
            ->when($search, fn ($q, $s) => $q->whereHas('recette', fn ($r) => $r
                ->where('title', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")
            ))
            ->when($categoryId, fn ($q, $id) => $q->whereHas('recette.categories', fn ($c) => $c
                ->where('categories.id', $id)
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::whereHas('recettes.favoris', fn ($q) => $q
            ->where('user_id', $request->user()->id)
        )->orderBy('name')->get();

        $user = $request->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('favorites.index', compact('favoris', 'categories', 'search', 'user', 'initials'));
    }

    public function store(StoreFavoriteRequest $request): RedirectResponse
    {
        $recipe = Recette::query()
            ->visibleTo($request->user())
            ->findOrFail($request->validated('recette_id'));

        $request->user()->favoris()->create([
            'recette_id' => $recipe->id,
        ]);

        return redirect()->back()->with('success', 'Recipe added to favorites.');
    }

    public function destroy(Favori $favori): RedirectResponse
    {
        $this->authorize('delete', $favori);

        $favori->delete();

        return redirect()->back()->with('success', 'Recipe removed from favorites.');
    }
}
