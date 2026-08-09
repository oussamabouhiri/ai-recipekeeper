@extends('layouts.app')

@section('title', 'My Recipes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Recipes</h1>
    <a href="{{ route('recipes.create') }}" class="btn btn-success">Create Recipe</a>
</div>

@if ($recipes->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <h5 class="card-title text-muted">No recipes yet</h5>
            <p class="card-text text-muted">Start by creating your first recipe.</p>
            <a href="{{ route('recipes.create') }}" class="btn btn-primary">Create Recipe</a>
        </div>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recipes as $recipe)
                    <tr>
                        <td>
                            <a href="{{ route('recipes.show', $recipe) }}" class="text-decoration-none fw-medium">
                                {{ $recipe->title }}
                            </a>
                        </td>
                        <td>
                            @if ($recipe->statut === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Hidden</span>
                            @endif
                        </td>
                        <td>{{ $recipe->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-outline-primary">View</a>
                                @can('update', $recipe)
                                    <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-outline-warning">Edit</a>
                                @endcan
                                @can('delete', $recipe)
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $recipe->id }}">Delete</button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    @can('delete', $recipe)
                        <div class="modal fade" id="deleteModal{{ $recipe->id }}" tabindex="-1">
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
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $recipes->links() }}
    </div>
@endif
@endsection
