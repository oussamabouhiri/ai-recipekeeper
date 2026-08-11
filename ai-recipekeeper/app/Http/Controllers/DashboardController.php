<?php

namespace App\Http\Controllers;

use App\Models\Recette;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $featured = Recette::query()
            ->visibleTo($user)
            ->with(['categories', 'avis'])
            ->latest()
            ->first();

        $ratingAvg = $featured?->avis->avg('rating');
        $ratingAvg = $ratingAvg === null ? null : round((float) $ratingAvg, 1);
        $ratingCount = $featured?->avis->count() ?? 0;

        $favoritesCount = $user->favoris()->count();
        $favoriteRecipes = $user->favoris()
            ->with('recette.categories')
            ->latest()
            ->limit(4)
            ->get();

        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour < 12 => 'Good Morning',
            $hour < 18 => 'Good Afternoon',
            default => 'Good Evening',
        };

        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return view('dashboard', compact(
            'user',
            'featured',
            'ratingAvg',
            'ratingCount',
            'favoritesCount',
            'favoriteRecipes',
            'greeting',
            'initials'
        ));
    }
}
