@extends('layouts.app')

@section('title', $recipe->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h1 class="h3 mb-1">{{ $recipe->title }}</h1>
                        <p class="text-muted mb-0">
                            By {{ $recipe->user->name }} &middot; {{ $recipe->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div>
                        @if ($recipe->statut === 'published')
                            <span class="badge bg-success fs-6">Published</span>
                        @else
                            <span class="badge bg-secondary fs-6">Hidden</span>
                        @endif
                    </div>
                </div>

                @if ($recipe->description)
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="mb-0">{{ $recipe->description }}</p>
                    </div>
                @endif

                <div class="row mb-4">
                    @if ($recipe->prep_time)
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Prep Time</small>
                            <strong>{{ $recipe->prep_time }} min</strong>
                        </div>
                    @endif
                    @if ($recipe->cook_time)
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Cook Time</small>
                            <strong>{{ $recipe->cook_time }} min</strong>
                        </div>
                    @endif
                    @if ($recipe->servings)
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Servings</small>
                            <strong>{{ $recipe->servings }}</strong>
                        </div>
                    @endif
                    @if ($recipe->difficulty)
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Difficulty</small>
                            <strong>{{ $recipe->difficulty }}</strong>
                        </div>
                    @endif
                </div>

                @if ($recipe->categories->isNotEmpty())
                    <div class="mb-4">
                        <h5>Categories</h5>
                        @foreach ($recipe->categories as $category)
                            <span class="badge bg-primary me-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                @endif

                @if ($recipe->ingredients->isNotEmpty())
                    <div class="mb-4">
                        <h5>Ingredients</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ingredient</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recipe->ingredients as $ingredient)
                                        <tr>
                                            <td>{{ $ingredient->name }}</td>
                                            <td>{{ $ingredient->pivot->quantity ?? '-' }}</td>
                                            <td>{{ $ingredient->pivot->unit ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($recipe->etapes->isNotEmpty())
                    <div class="mb-4">
                        <h5>Steps</h5>
                        @foreach ($recipe->etapes->sortBy('step_number') as $etape)
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <span class="badge bg-dark rounded-circle p-2">{{ $etape->step_number }}</span>
                                </div>
                                <div>
                                    <p class="mb-0">{{ $etape->instruction }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary">Back to Recipes</a>
                    @if ($favorite)
                        <form action="{{ route('favorites.destroy', $favorite) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-warning w-100">Remove from Favorites</button>
                        </form>
                    @else
                        <form action="{{ route('favorites.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="recette_id" value="{{ $recipe->id }}">
                            <button type="submit" class="btn btn-outline-warning w-100">Add to Favorites</button>
                        </form>
                    @endif
                    @can('update', $recipe)
                        <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-warning">Edit Recipe</a>
                    @endcan
                    @can('delete', $recipe)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Recipe</button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

@can('delete', $recipe)
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Recipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete "{{ $recipe->title }}"? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('recipes.destroy', $recipe) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan
@endsection
