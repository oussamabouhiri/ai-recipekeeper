@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add Category</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        @if ($categories->isEmpty())
            <div class="text-center text-muted py-4">
                No categories found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Recipes</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>{{ $category->recettes_count ?? $category->recettes->count() }}</td>
                                <td>{{ $category->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    {{ $categories->links() }}
</div>
@endsection
