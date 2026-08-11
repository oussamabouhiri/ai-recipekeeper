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
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
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

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Reviews</h2>
            @if ($ratingAvg !== null)
                <span class="text-muted">{{ $ratingAvg }} / 5 &middot; {{ $ratingCount }} {{ Str::plural('review', $ratingCount) }}</span>
            @else
                <span class="text-muted">No reviews yet</span>
            @endif
        </div>

        @if ($userReview)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $userReview->user->name }}</strong>
                        <div class="text-warning">{{ $userReview->rating }}/5</div>
                        @if ($userReview->comment)
                            <p class="mb-0">{{ $userReview->comment }}</p>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editReviewForm">Edit Review</button>
                        <form action="{{ route('reviews.destroy', $userReview) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete Review</button>
                        </form>
                    </div>
                </div>
                <div class="collapse mt-3" id="editReviewForm">
                    <form action="{{ route('reviews.update', $userReview) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-2">
                            <label class="form-label" for="edit-rating">Rating</label>
                            <select name="rating" id="edit-rating" class="form-select">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected($userReview->rating === $i)>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="edit-comment">Comment</label>
                            <textarea name="comment" id="edit-comment" class="form-control" rows="2">{{ old('comment', $userReview->comment) }}</textarea>
                            @error('comment')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Update Review</button>
                    </form>
                </div>
            </div>
        @else
            <form action="{{ route('reviews.store', $recipe) }}" method="POST" class="mb-3">
                @csrf
                <div class="mb-2">
                    <label class="form-label" for="rating">Rating</label>
                    <select name="rating" id="rating" class="form-select">
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    @error('rating')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label" for="comment">Comment</label>
                    <textarea name="comment" id="comment" class="form-control" rows="2" placeholder="Share your thoughts (optional)"></textarea>
                    @error('comment')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        @endif

        @foreach ($recipe->avis as $review)
            @if ($userReview && $review->id === $userReview->id)
                @continue
            @endif
            <div class="border-top pt-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $review->user->name }}</strong>
                        <span class="text-muted small">&middot; {{ $review->created_at->format('M d, Y') }}</span>
                        <div class="text-warning">{{ $review->rating }}/5</div>
                        @if ($review->comment)
                            <p class="mb-0">{{ $review->comment }}</p>
                        @endif
                    </div>
                    @can('delete', $review)
                        <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete Review</button>
                        </form>
                    @endcan
                </div>
            </div>
        @endforeach
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
