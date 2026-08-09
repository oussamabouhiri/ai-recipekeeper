<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvisRequest;
use App\Http\Requests\UpdateAvisRequest;
use App\Models\Avis;
use App\Models\Recette;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class AvisWebController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreAvisRequest $request, Recette $recipe): RedirectResponse
    {
        $recipe = Recette::query()
            ->visibleTo($request->user())
            ->findOrFail($recipe->id);

        $recipe->avis()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }

    public function update(UpdateAvisRequest $request, Avis $avis): RedirectResponse
    {
        $this->authorize('update', $avis);

        $avis->update($request->validated());

        return redirect()->back()->with('success', 'Review updated successfully.');
    }

    public function destroy(Avis $avis): RedirectResponse
    {
        $this->authorize('delete', $avis);

        $avis->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
