@extends('layouts.app')

@section('title', 'Generate Recipe with AI')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Generate Recipe with AI</h1>
            <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary">Back to Recipes</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('generations.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Ingredients <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2">Add the ingredients you have available. The AI will create a recipe using them.</p>

                        <div id="ingredients-container">
                            <div class="input-group mb-2 ingredient-row">
                                <input type="text" name="ingredients[0][name]" class="form-control" placeholder="Ingredient name (e.g., chicken breast)" required>
                                <input type="text" name="ingredients[0][quantity]" class="form-control" style="max-width: 100px;" placeholder="Qty">
                                <input type="text" name="ingredients[0][unit]" class="form-control" style="max-width: 100px;" placeholder="Unit">
                                <button type="button" class="btn btn-outline-danger remove-ingredient" style="display: none;">&times;</button>
                            </div>
                        </div>

                        <button type="button" id="add-ingredient" class="btn btn-outline-success btn-sm mt-2">
                            + Add Ingredient
                        </button>

                        @error('ingredients')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('ingredients.*.name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="preferences" class="form-label fw-bold">Preferences</label>
                        <textarea name="preferences" id="preferences" class="form-control" rows="3" placeholder="e.g., Quick weeknight dinner, healthy, low-carb, kid-friendly...">{{ old('preferences') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="constraints" class="form-label fw-bold">Constraints</label>
                        <textarea name="constraints" id="constraints" class="form-control" rows="2" placeholder="e.g., Nut allergy, no dairy, gluten-free...">{{ old('constraints') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="servings" class="form-label fw-bold">Servings</label>
                            <input type="number" name="servings" id="servings" class="form-control" min="1" max="100" value="{{ old('servings', 4) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="difficulty" class="form-label fw-bold">Difficulty</label>
                            <select name="difficulty" id="difficulty" class="form-select">
                                <option value="">Any</option>
                                <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            Generate Recipe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('ingredients-container');
        const addBtn = document.getElementById('add-ingredient');
        let rowIndex = 1;

        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'input-group mb-2 ingredient-row';
            row.innerHTML = `
                <input type="text" name="ingredients[${rowIndex}][name]" class="form-control" placeholder="Ingredient name" required>
                <input type="text" name="ingredients[${rowIndex}][quantity]" class="form-control" style="max-width: 100px;" placeholder="Qty">
                <input type="text" name="ingredients[${rowIndex}][unit]" class="form-control" style="max-width: 100px;" placeholder="Unit">
                <button type="button" class="btn btn-outline-danger remove-ingredient">&times;</button>
            `;
            container.appendChild(row);
            rowIndex++;
            updateRemoveButtons();
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-ingredient')) {
                e.target.closest('.ingredient-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = container.querySelectorAll('.ingredient-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-ingredient');
                removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
            });
        }
    });
</script>
@endsection
