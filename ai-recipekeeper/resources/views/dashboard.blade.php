@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">Dashboard</h1>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Recipes</h5>
                        <p class="card-text display-6">{{ Auth::user()->recettes()->count() }}</p>
                        <a href="{{ route('recipes.index') }}" class="btn btn-primary">View Recipes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create Recipe</h5>
                        <p class="card-text text-muted">Start a new recipe</p>
                        <a href="{{ route('recipes.create') }}" class="btn btn-success">New Recipe</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
