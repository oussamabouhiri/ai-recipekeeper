@extends('layouts.app')

@section('title', 'Create Recipe')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <h1 class="h3 mb-4">Create Recipe</h1>

        <form action="{{ route('recipes.store') }}" method="POST">
            @csrf

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Basic Information</h5>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="prep_time" class="form-label">Prep Time (min)</label>
                            <input type="number" class="form-control @error('prep_time') is-invalid @enderror" id="prep_time" name="prep_time" value="{{ old('prep_time') }}" min="0">
                            @error('prep_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cook_time" class="form-label">Cook Time (min)</label>
                            <input type="number" class="form-control @error('cook_time') is-invalid @enderror" id="cook_time" name="cook_time" value="{{ old('cook_time') }}" min="0">
                            @error('cook_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="servings" class="form-label">Servings</label>
                            <input type="number" class="form-control @error('servings') is-invalid @enderror" id="servings" name="servings" value="{{ old('servings') }}" min="1">
                            @error('servings')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="difficulty" class="form-label">Difficulty</label>
                            <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty">
                                <option value="">Select...</option>
                                <option value="Easy" {{ old('difficulty') === 'Easy' ? 'selected' : '' }}>Easy</option>
                                <option value="Medium" {{ old('difficulty') === 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Hard" {{ old('difficulty') === 'Hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="statut" class="form-label">Status</label>
                        <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut">
                            <option value="published" {{ old('statut', 'published') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="hidden" {{ old('statut') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <div class="mb-3">
                        <select class="form-select @error('categories') is-invalid @enderror" id="categories" name="categories[]" multiple size="4">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple categories.</small>
                        @error('categories')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Ingredients</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addIngredient">+ Add Ingredient</button>
                    </div>

                    <div id="ingredients-container">
                    </div>

                    <template id="ingredient-template">
                        <div class="ingredient-row row mb-2">
                            <div class="col-md-5">
                                <select class="form-select" name="ingredients[__index__][ingredient_id]" required>
                                    <option value="">Select ingredient...</option>
                                    @foreach ($ingredients as $ingredient)
                                        <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="ingredients[__index__][quantity]" placeholder="Quantity">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="ingredients[__index__][unit]" placeholder="Unit">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-ingredient">Remove</button>
                            </div>
                        </div>
                    </template>

                    @error('ingredients')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                    @error('ingredients.*.ingredient_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Steps</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addStep">+ Add Step</button>
                    </div>

                    <div id="steps-container">
                    </div>

                    <template id="step-template">
                        <div class="step-row row mb-2">
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="etapes[__index__][step_number]" placeholder="#" min="1" required>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control" name="etapes[__index__][instruction]" placeholder="Instruction" rows="2" required></textarea>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-step">Remove</button>
                            </div>
                        </div>
                    </template>

                    @error('etapes')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                    @error('etapes.*.instruction')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Create Recipe</button>
                <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let ingredientIndex = 0;
    let stepIndex = 0;

    document.getElementById('addIngredient').addEventListener('click', function() {
        const container = document.getElementById('ingredients-container');
        const template = document.getElementById('ingredient-template');
        const clone = template.content.cloneNode(true);

        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__index__', ingredientIndex);
        });

        container.appendChild(clone);
        ingredientIndex++;
    });

    document.getElementById('addStep').addEventListener('click', function() {
        const container = document.getElementById('steps-container');
        const template = document.getElementById('step-template');
        const clone = template.content.cloneNode(true);

        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__index__', stepIndex);
        });

        container.appendChild(clone);
        stepIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-ingredient')) {
            e.target.closest('.ingredient-row').remove();
        }
        if (e.target.classList.contains('remove-step')) {
            e.target.closest('.step-row').remove();
        }
    });
});
</script>
@endsection
