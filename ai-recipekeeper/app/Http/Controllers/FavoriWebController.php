<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteRequest;
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
        $favoris = $request->user()->favoris()
            ->with('recette.user', 'recette.categories')
            ->latest()
            ->paginate();

        return view('favorites.index', compact('favoris'));
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
