@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Favorites</h1>
</div>

@if ($favoris->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <h5 class="card-title text-muted">No favorites yet</h5>
            <p class="card-text text-muted">Browse recipes and add them to your favorites.</p>
            <a href="{{ route('recipes.index') }}" class="btn btn-primary">Browse Recipes</a>
        </div>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>By</th>
                    <th>Status</th>
                    <th>Added</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($favoris as $favori)
                    <tr>
                        <td>
                            <a href="{{ route('recipes.show', $favori->recette) }}" class="text-decoration-none fw-medium">
                                {{ $favori->recette->title }}
                            </a>
                        </td>
                        <td>{{ $favori->recette->user->name }}</td>
                        <td>
                            @if ($favori->recette->statut === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Hidden</span>
                            @endif
                        </td>
                        <td>{{ $favori->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('recipes.show', $favori->recette) }}" class="btn btn-outline-primary">View</a>
                                <form action="{{ route('favorites.destroy', $favori) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $favoris->links() }}
    </div>
@endif
@endsection
