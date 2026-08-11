@extends('layouts.dashboard')

@section('title', 'Edit Recipe')

@section('content')
<header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-5">
    <div>
        <h1 class="font-display-lg text-display-lg hidden md:block text-on-surface">Edit Recipe</h1>
        <h1 class="font-display-lg text-display-lg md:hidden text-on-surface">Edit Recipe</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant mt-1 max-w-2xl">Update your recipe details. Make changes below to save your updated recipe.</p>
    </div>
    <div class="flex gap-3 shrink-0">
        <a href="{{ route('recipes.index') }}" class="font-label-md text-label-md px-6 py-3 rounded-lg border border-primary text-primary hover:bg-surface-container-low transition-colors">Cancel</a>
        <button type="submit" form="recipe-form" class="font-label-md text-label-md px-6 py-3 rounded-lg bg-primary text-on-primary shadow-sm hover:bg-surface-tint transition-colors">Update Recipe</button>
    </div>
</header>

<form id="recipe-form" action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
    @csrf
    @method('PUT')

    {{-- Left Column: Main Content --}}
    <div class="lg:col-span-8 flex flex-col gap-4">

        {{-- Basic Details Card --}}
        <section class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-outline-variant/30 p-6">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary" style="font-size: 22px;">edit_document</span>
                Basic Details
            </h2>
            <div class="flex flex-col gap-5">
                {{-- Title --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface" for="title">Recipe Title <span class="text-error">*</span></label>
                    <input
                        type="text"
                        class="w-full bg-surface text-on-surface border rounded-lg px-4 py-3 font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none @error('title') border-error @else border-outline-variant @enderror"
                        id="title"
                        name="title"
                        value="{{ old('title', $recipe->title) }}"
                        placeholder="e.g., Rustic Sourdough Loaf"
                        required
                    >
                    @error('title')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface" for="description">Description</label>
                    <textarea
                        class="w-full bg-surface text-on-surface border rounded-lg px-4 py-3 font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none resize-y @error('description') border-error @else border-outline-variant @enderror"
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="A brief description of what makes this recipe special..."
                    >{{ old('description', $recipe->description) }}</textarea>
                    @error('description')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cover Image --}}
                <div class="flex flex-col gap-1">
                    <span class="font-label-md text-label-md text-on-surface mb-1">Cover Image</span>
                    <div id="image-dropzone" class="border-2 border-dashed border-outline-variant rounded-xl p-8 flex flex-col items-center justify-center text-center bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group relative">
                        <div id="image-placeholder" class="{{ $recipe->image_path ? 'hidden' : '' }}">
                            <span class="material-symbols-outlined text-4xl text-outline group-hover:text-primary transition-colors mb-3" style="font-size: 36px;">add_photo_alternate</span>
                            <p class="font-label-md text-label-md text-on-surface">Click to upload or drag and drop</p>
                            <p class="font-caption text-caption text-outline mt-1">PNG, JPG or GIF (max. 2MB)</p>
                        </div>
                        <img id="image-preview" class="{{ $recipe->image_path ? '' : 'hidden' }} max-h-48 rounded-lg object-cover" src="{{ $recipe->image_path ? asset($recipe->image_path) : '' }}" alt="Preview">
                        <button type="button" id="image-remove" class="{{ $recipe->image_path ? '' : 'hidden' }} absolute top-2 right-2 p-1 rounded-full bg-error text-on-error hover:bg-error/80 transition-colors">
                            <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
                        </button>
                    </div>
                    <input type="file" id="image-input" name="image_path" accept="image/*" class="hidden">
                    <input type="hidden" id="image_path_delete" name="image_path_delete" value="">
                    @error('image_path')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Difficulty --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface" for="difficulty">Difficulty</label>
                    <select
                        class="w-full bg-surface text-on-surface border rounded-lg px-4 py-3 font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none appearance-none cursor-pointer @error('difficulty') border-error @else border-outline-variant @enderror"
                        id="difficulty"
                        name="difficulty"
                    >
                        <option value="">Select difficulty...</option>
                        <option value="Easy" {{ old('difficulty', $recipe->difficulty) === 'Easy' ? 'selected' : '' }}>Easy</option>
                        <option value="Medium" {{ old('difficulty', $recipe->difficulty) === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Hard" {{ old('difficulty', $recipe->difficulty) === 'Hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                    @error('difficulty')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface" for="statut">Status</label>
                    <select
                        class="w-full bg-surface text-on-surface border rounded-lg px-4 py-3 font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none appearance-none cursor-pointer @error('statut') border-error @else border-outline-variant @enderror"
                        id="statut"
                        name="statut"
                    >
                        <option value="published" {{ old('statut', $recipe->statut) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="hidden" {{ old('statut', $recipe->statut) === 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>
                    @error('statut')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Ingredients Card --}}
        <section class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-outline-variant/30 p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary" style="font-size: 22px;">grocery</span>
                    Ingredients
                </h2>
            </div>

            <div class="flex flex-col gap-3" id="ingredients-container">
                @foreach ($recipe->ingredients as $index => $ingredient)
                    <div class="ingredient-row flex items-start gap-3 group">
                        <div class="w-1/4">
                            <input
                                type="text"
                                class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none"
                                name="ingredients[{{ $index }}][quantity]"
                                placeholder="Amount (e.g., 2 cups)"
                                value="{{ old('ingredients.'.$index.'.quantity', $ingredient->pivot->quantity) }}"
                            >
                        </div>
                        <div class="flex-grow relative">
                            <select
                                class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none"
                                name="ingredients[{{ $index }}][ingredient_id]"
                            >
                                <option value="">Select ingredient...</option>
                                @foreach ($ingredients as $ing)
                                    <option value="{{ $ing->id }}" {{ $ing->id === $ingredient->id ? 'selected' : '' }}>{{ $ing->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input
                            type="text"
                            class="w-24 bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none"
                            name="ingredients[{{ $index }}][unit]"
                            placeholder="Unit"
                            value="{{ old('ingredients.'.$index.'.unit', $ingredient->pivot->unit) }}"
                        >
                        <button type="button" aria-label="Remove ingredient" class="remove-ingredient p-2 text-outline hover:text-error transition-colors rounded-lg hover:bg-error-container/20 mt-0.5">
                            <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                        </button>
                    </div>
                @endforeach
            </div>

            <template id="ingredient-template">
                <div class="ingredient-row flex items-start gap-3 group">
                    <div class="w-1/4">
                        <input
                            type="text"
                            class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none"
                            name="ingredients[__index__][quantity]"
                            placeholder="Amount (e.g., 2 cups)"
                        >
                    </div>
                    <div class="flex-grow relative">
                        <select
                            class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none"
                            name="ingredients[__index__][ingredient_id]"
                        >
                            <option value="">Select ingredient...</option>
                            @foreach ($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input
                        type="text"
                        class="w-24 bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none"
                        name="ingredients[__index__][unit]"
                        placeholder="Unit"
                    >
                    <button type="button" aria-label="Remove ingredient" class="remove-ingredient p-2 text-outline hover:text-error transition-colors rounded-lg hover:bg-error-container/20 mt-0.5">
                        <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                    </button>
                </div>
            </template>

            @error('ingredients')
                <p class="text-error text-sm mt-2">{{ $message }}</p>
            @enderror
            @error('ingredients.*.ingredient_id')
                <p class="text-error text-sm mt-2">{{ $message }}</p>
            @enderror

            <button
                type="button"
                id="addIngredient"
                class="mt-5 flex items-center justify-center gap-2 w-full py-3 border-2 border-dashed border-outline-variant rounded-lg text-primary font-label-md text-label-md hover:bg-primary-container/10 hover:border-primary/50 transition-colors"
            >
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span> Add Ingredient
            </button>
        </section>

        {{-- Preparation Steps Card --}}
        <section class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-outline-variant/30 p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary" style="font-size: 22px;">format_list_numbered</span>
                    Preparation Steps
                </h2>
            </div>

            <div class="flex flex-col gap-5" id="steps-container">
                @foreach ($recipe->etapes->sortBy('step_number') as $index => $etape)
                    <div class="step-row flex gap-3 relative pl-12 group">
                        <div class="step-number absolute left-0 top-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-label-md text-label-md">{{ $loop->iteration }}</div>
                        <div class="flex-grow flex flex-col gap-1">
                            <textarea
                                class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-4 py-3 font-body-md focus:ring-2 focus:ring-primary outline-none resize-y"
                                name="etapes[{{ $index }}][instruction]"
                                placeholder="Describe this step..."
                                rows="3"
                                required
                            >{{ old('etapes.'.$index.'.instruction', $etape->instruction) }}</textarea>
                        </div>
                        <div class="flex flex-col gap-1 shrink-0">
                            <button aria-label="Remove step" class="remove-step p-2 text-outline hover:text-error transition-colors rounded-lg hover:bg-error-container/20" type="button">
                                <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                            </button>
                        </div>
                        <input type="hidden" name="etapes[{{ $index }}][step_number]" class="step-number-input" value="{{ $etape->step_number }}">
                    </div>
                @endforeach
            </div>

            <template id="step-template">
                <div class="step-row flex gap-3 relative pl-12 group">
                    <div class="step-number absolute left-0 top-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-label-md text-label-md"></div>
                    <div class="flex-grow flex flex-col gap-1">
                        <textarea
                            class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-4 py-3 font-body-md focus:ring-2 focus:ring-primary outline-none resize-y"
                            name="etapes[__index__][instruction]"
                            placeholder="Describe this step..."
                            rows="3"
                            required
                        ></textarea>
                    </div>
                    <div class="flex flex-col gap-1 shrink-0">
                        <button aria-label="Remove step" class="remove-step p-2 text-outline hover:text-error transition-colors rounded-lg hover:bg-error-container/20" type="button">
                            <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                        </button>
                    </div>
                    <input type="hidden" name="etapes[__index__][step_number]" class="step-number-input" value="">
                </div>
            </template>

            @error('etapes')
                <p class="text-error text-sm mt-2">{{ $message }}</p>
            @enderror
            @error('etapes.*.instruction')
                <p class="text-error text-sm mt-2">{{ $message }}</p>
            @enderror

            <button
                type="button"
                id="addStep"
                class="mt-5 flex items-center justify-center gap-2 w-full py-3 border-2 border-dashed border-outline-variant rounded-lg text-primary font-label-md text-label-md hover:bg-primary-container/10 hover:border-primary/50 transition-colors"
            >
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span> Add Step
            </button>
        </section>

    </div>

    {{-- Right Column: Sidebar --}}
    <aside class="lg:col-span-4 flex flex-col gap-4 lg:sticky lg:top-24">

        {{-- Timing & Yield Card --}}
        <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-outline-variant/30 p-6">
            <h3 class="font-headline-md text-[20px] text-on-surface mb-5 border-b border-outline-variant/30 pb-3">Timing &amp; Yield</h3>
            <div class="flex flex-col gap-3">
                <div class="grid grid-cols-2 gap-3">
                    {{-- Prep Time --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="prep_time">Prep Time</label>
                        <div class="relative">
                            <input
                                type="number"
                                class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg pl-3 pr-12 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none @error('prep_time') border-error @enderror"
                                id="prep_time"
                                name="prep_time"
                                value="{{ old('prep_time', $recipe->prep_time) }}"
                                placeholder="0"
                                min="0"
                            >
                            <span class="absolute right-3 top-2 text-outline font-body-md text-body-md">min</span>
                        </div>
                        @error('prep_time')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Cook Time --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="cook_time">Cook Time</label>
                        <div class="relative">
                            <input
                                type="number"
                                class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg pl-3 pr-12 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none @error('cook_time') border-error @enderror"
                                id="cook_time"
                                name="cook_time"
                                value="{{ old('cook_time', $recipe->cook_time) }}"
                                placeholder="0"
                                min="0"
                            >
                            <span class="absolute right-3 top-2 text-outline font-body-md text-body-md">min</span>
                        </div>
                        @error('cook_time')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                {{-- Servings --}}
                <div class="flex flex-col gap-1 mt-1">
                    <label class="font-label-md text-label-md text-on-surface" for="servings">Servings</label>
                    <input
                        type="number"
                        class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg px-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none @error('servings') border-error @enderror"
                        id="servings"
                        name="servings"
                        value="{{ old('servings', $recipe->servings) }}"
                        placeholder="e.g., 4"
                        min="1"
                    >
                    @error('servings')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Categorization Card --}}
        <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-outline-variant/30 p-6">
            <h3 class="font-headline-md text-[20px] text-on-surface mb-5 border-b border-outline-variant/30 pb-3">Categorization</h3>
            <div class="flex flex-col gap-3">
                {{-- Category selector --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface">Categories</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2 text-outline" style="font-size: 20px;">sell</span>
                        <select
                            id="category-select"
                            class="w-full bg-surface text-on-surface border border-outline-variant rounded-lg pl-10 pr-3 py-2 font-body-md focus:ring-2 focus:ring-primary outline-none appearance-none cursor-pointer"
                        >
                            <option value="">Select a category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="category-chips" class="flex flex-wrap gap-2 mt-2">
                        @foreach ($recipe->categories as $cat)
                            <span class="inline-flex items-center gap-1 bg-primary-container/20 text-on-primary-container px-3 py-1 rounded-full font-caption text-caption" data-cat-id="{{ $cat->id }}">
                                {{ $cat->name }} <button type="button" class="hover:text-primary remove-category-chip"><span class="material-symbols-outlined" style="font-size: 14px;">close</span></button>
                            </span>
                        @endforeach
                    </div>
                    {{-- Hidden inputs for selected categories --}}
                    <div id="category-inputs">
                        @foreach ($recipe->categories as $cat)
                            <input type="hidden" name="categories[]" value="{{ $cat->id }}" data-cat-id="{{ $cat->id }}">
                        @endforeach
                    </div>
                    @error('categories')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Sticky Submit Actions for Desktop --}}
        <div class="hidden lg:flex flex-col gap-3 mt-2">
            <button type="submit" class="font-label-md text-label-md w-full py-3 rounded-lg bg-primary text-on-primary shadow-sm hover:bg-surface-tint transition-colors flex justify-center items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span> Update Recipe
            </button>
            <a href="{{ route('recipes.index') }}" class="font-label-md text-label-md w-full py-3 rounded-lg border border-outline text-on-surface hover:bg-surface-container-low transition-colors flex justify-center items-center">
                Cancel
            </a>
        </div>
    </aside>
</form>

{{-- Danger Zone --}}
<section class="max-w-4xl mx-auto mt-6 border border-error/30 bg-error-container/10 rounded-xl p-6">
    <h3 class="font-headline-md text-headline-md text-error flex items-center gap-2 mb-2">
        <span class="material-symbols-outlined" style="font-size: 22px;">warning</span> Danger Zone
    </h3>
    <p class="font-body-md text-body-md text-on-surface-variant mb-5">Once you delete a recipe, there is no going back. Please be certain.</p>
    <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="font-label-md text-label-md px-6 py-2 rounded-lg border border-error text-error hover:bg-error hover:text-on-error transition-colors" onclick="return confirm('Are you sure you want to delete this recipe? This action cannot be undone.')">
            Delete Recipe
        </button>
    </form>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Image Upload Dropzone ---
    const dropzone = document.getElementById('image-dropzone');
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    const imagePlaceholder = document.getElementById('image-placeholder');
    const imageRemove = document.getElementById('image-remove');
    const imagePathDelete = document.getElementById('image_path_delete');

    dropzone.addEventListener('click', function() {
        imageInput.click();
    });

    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-primary-container/5');
    });

    dropzone.addEventListener('dragleave', function() {
        dropzone.classList.remove('border-primary', 'bg-primary-container/5');
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-primary-container/5');
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            imageInput.files = files;
            showPreview(files[0]);
        }
    });

    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            showPreview(this.files[0]);
        }
    });

    imageRemove.addEventListener('click', function(e) {
        e.stopPropagation();
        imageInput.value = '';
        imagePathDelete.value = '1';
        imagePreview.classList.add('hidden');
        imagePlaceholder.classList.remove('hidden');
        imageRemove.classList.add('hidden');
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.classList.remove('hidden');
            imagePlaceholder.classList.add('hidden');
            imageRemove.classList.remove('hidden');
            imagePathDelete.value = '';
        };
        reader.readAsDataURL(file);
    }

    // --- Ingredients ---
    let ingredientIndex = {{ $recipe->ingredients->count() }};
    const ingredientsContainer = document.getElementById('ingredients-container');
    const ingredientTemplate = document.getElementById('ingredient-template');

    document.getElementById('addIngredient').addEventListener('click', function() {
        const clone = ingredientTemplate.content.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__index__', ingredientIndex);
        });
        ingredientsContainer.appendChild(clone);
        ingredientIndex++;
    });

    // --- Steps ---
    let stepIndex = {{ $recipe->etapes->count() }};
    const stepsContainer = document.getElementById('steps-container');
    const stepTemplate = document.getElementById('step-template');

    function renumberSteps() {
        const rows = stepsContainer.querySelectorAll('.step-row');
        rows.forEach((row, i) => {
            const num = i + 1;
            const circle = row.querySelector('.step-number');
            if (circle) circle.textContent = num;
            const hiddenInput = row.querySelector('.step-number-input');
            if (hiddenInput) hiddenInput.value = num;
        });
    }

    document.getElementById('addStep').addEventListener('click', function() {
        const clone = stepTemplate.content.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__index__', stepIndex);
        });
        stepsContainer.appendChild(clone);
        stepIndex++;
        renumberSteps();
    });

    // --- Remove delegation ---
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-ingredient');
        if (removeBtn) {
            removeBtn.closest('.ingredient-row').remove();
            return;
        }
        const removeStepBtn = e.target.closest('.remove-step');
        if (removeStepBtn) {
            removeStepBtn.closest('.step-row').remove();
            renumberSteps();
        }
    });

    // --- Category chips ---
    const categorySelect = document.getElementById('category-select');
    const categoryChips = document.getElementById('category-chips');
    const categoryInputs = document.getElementById('category-inputs');
    const selectedCategories = new Map();

    // Initialize from existing categories
    @foreach ($recipe->categories as $cat)
        selectedCategories.set('{{ $cat->id }}', '{{ $cat->name }}');
    @endforeach

    categorySelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (!option.value) return;

        const catId = option.value;
        const catName = option.text;

        if (selectedCategories.has(catId)) {
            this.value = '';
            return;
        }

        selectedCategories.set(catId, catName);

        // Add hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'categories[]';
        input.value = catId;
        input.dataset.catId = catId;
        categoryInputs.appendChild(input);

        // Add chip
        const chip = document.createElement('span');
        chip.className = 'inline-flex items-center gap-1 bg-primary-container/20 text-on-primary-container px-3 py-1 rounded-full font-caption text-caption';
        chip.dataset.catId = catId;
        chip.innerHTML = catName + ' <button type="button" class="hover:text-primary remove-category-chip"><span class="material-symbols-outlined" style="font-size: 14px;">close</span></button>';
        categoryChips.appendChild(chip);

        this.value = '';
    });

    categoryChips.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-category-chip');
        if (!btn) return;
        const chip = btn.closest('[data-cat-id]');
        const catId = chip.dataset.catId;
        selectedCategories.delete(catId);
        chip.remove();
        const input = categoryInputs.querySelector('[data-cat-id="' + catId + '"]');
        if (input) input.remove();
    });
});
</script>
@endsection
