@extends('layouts.dashboard')

@section('title', 'Generate Recipe with AI')

@section('content')
<div class="flex flex-col gap-12">

    {{-- Hero Section --}}
    <section class="text-center max-w-2xl mx-auto">
        <h1 class="font-display-lg text-[28px] leading-[36px] md:text-[32px] md:leading-[40px] lg:text-[48px] lg:leading-[56px] text-on-surface mb-3 font-bold tracking-tight">Generate Your Next Masterpiece</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Let AI craft the perfect recipe based on what you have, what you crave, and how much time you have.</p>
    </section>

    {{-- Bento Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left Column: Form --}}
        <div class="lg:col-span-7 space-y-6 relative">

            <form method="POST" action="{{ route('generations.store') }}" id="generation-form">
                @csrf

                {{-- Ingredients Card --}}
                <div class="bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl p-6">
                    <h2 class="font-headline-md text-headline-md text-primary mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined">kitchen</span>
                        Ingredients on Hand
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6">What's in your pantry or fridge?</p>

                    <div id="ingredients-container" class="space-y-3">
                        <div class="flex gap-2 items-center ingredient-row">
                            <input type="text" name="ingredients[0][name]" class="flex-1 bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md" placeholder="Ingredient name (e.g., chicken breast)" required>
                            <input type="text" name="ingredients[0][quantity]" class="w-24 bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md" placeholder="Qty">
                            <input type="text" name="ingredients[0][unit]" class="w-24 bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md" placeholder="Unit">
                            <button type="button" class="p-2 text-on-surface-variant hover:text-error transition-colors remove-ingredient hidden">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    </div>

                    <button type="button" id="add-ingredient" class="mt-4 text-primary font-label-md text-label-md flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-sm">add</span> Add Ingredient
                    </button>

                    @error('ingredients')
                        <div class="text-error text-sm mt-2">{{ $message }}</div>
                    @enderror
                    @error('ingredients.*.name')
                        <div class="text-error text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Preferences & Constraints Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Preferences Card --}}
                    <div class="bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl p-6">
                        <h2 class="font-headline-md text-headline-md text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined">tune</span>
                            Preferences
                        </h2>
                        <textarea name="preferences" id="preferences" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md resize-none" rows="4" placeholder="e.g., Quick weeknight dinner, healthy, low-carb, kid-friendly...">{{ old('preferences') }}</textarea>
                        @error('preferences')
                            <div class="text-error text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Constraints Card --}}
                    <div class="bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl p-6">
                        <h2 class="font-headline-md text-headline-md text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined">schedule</span>
                            Constraints
                        </h2>
                        <textarea name="constraints" id="constraints" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md resize-none" rows="4" placeholder="e.g., Nut allergy, no dairy, gluten-free...">{{ old('constraints') }}</textarea>
                        @error('constraints')
                            <div class="text-error text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Servings & Difficulty Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Servings Card --}}
                    <div class="bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl p-6">
                        <h2 class="font-headline-md text-headline-md text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined">group</span>
                            Servings
                        </h2>
                        <div class="flex items-center gap-4">
                            <input type="range" name="servings" id="servings" min="1" max="12" value="{{ old('servings', 4) }}" class="flex-1 accent-primary h-2 bg-surface-container-high rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('servings-val').innerText = this.value">
                            <span class="font-headline-md text-headline-md text-primary w-8 text-center" id="servings-val">{{ old('servings', 4) }}</span>
                        </div>
                    </div>

                    {{-- Difficulty Card --}}
                    <div class="bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl p-6">
                        <h2 class="font-headline-md text-headline-md text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined">signal_cellular_alt</span>
                            Difficulty
                        </h2>
                        <input type="hidden" name="difficulty" id="difficulty" value="{{ old('difficulty') }}">
                        <div class="flex gap-2" id="difficulty-buttons">
                            <button type="button" data-value="easy" class="flex-1 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-label-md text-label-md hover:bg-surface-container-low transition-colors difficulty-btn">Easy</button>
                            <button type="button" data-value="medium" class="flex-1 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-label-md text-label-md hover:bg-surface-container-low transition-colors difficulty-btn">Medium</button>
                            <button type="button" data-value="hard" class="flex-1 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-label-md text-label-md hover:bg-surface-container-low transition-colors difficulty-btn">Hard</button>
                        </div>
                        @error('difficulty')
                            <div class="text-error text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Generate Button --}}
                <button type="submit" id="generate-btn" class="w-full py-4 bg-primary text-on-primary rounded-xl font-headline-md text-headline-md flex items-center justify-center gap-2 shadow-lg hover:bg-surface-tint transition-all active:scale-[0.98]">
                    <span class="material-symbols-outlined">auto_awesome</span>
                    Generate Recipe
                </button>

            </form>

            {{-- Loading Overlay --}}
            <div id="loading-overlay" class="hidden absolute inset-0 bg-surface/90 backdrop-blur-sm rounded-xl z-20 flex flex-col items-center justify-center p-6">
                <div class="w-16 h-16 border-4 border-surface-container-high border-t-primary rounded-full animate-spin mb-6"></div>
                <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Crafting Your Recipe...</h3>
                <p class="font-body-md text-body-md text-on-surface-variant animate-pulse">Consulting the culinary models.</p>
            </div>

        </div>

        {{-- Right Column: AI Preview Panel --}}
        <div class="lg:col-span-5">
            <div class="sticky top-24 bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl overflow-hidden h-[600px] flex flex-col justify-center items-center text-center p-6 relative group">

                {{-- Atmospheric Background --}}
                <div class="absolute inset-0 bg-cover bg-center opacity-15 transition-opacity group-hover:opacity-20" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuA6wDgwpF8hiQh4AUlSlLKKgS7URcSDXAaSLDyHDI_FRVWnyOmL-5r_xaM6-qE1_1o3VsnvjFygwuYR0q1e701oS72hs_yvHnmlbkrfG3hLTY8H2VKMTWPAVritXLlIfYnW1QLHGlqU9zWY3qgRNShgiCYEoKRAQCbBEmBPtkZet8GF05m3qpSo7PPgvb7dKq7Z7I6jDx-zzHYOjXJxsmzbp-8lUFCyFhJZR3fBhJYnuNeio3t4bkVQCQ')"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-surface-container-low/60 via-surface/40 to-surface-container-low/60"></div>

                {{-- Content --}}
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center text-primary mb-6 shadow-sm">
                        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-3">Ready to Cook?</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-sm">Fill out your ingredients and preferences, and our culinary AI will craft a custom recipe just for you.</p>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ingredient Management
        const container = document.getElementById('ingredients-container');
        const addBtn = document.getElementById('add-ingredient');
        let rowIndex = 1;

        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'flex gap-2 items-center ingredient-row';
            row.innerHTML = `
                <input type="text" name="ingredients[${rowIndex}][name]" class="flex-1 bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md" placeholder="Ingredient name" required>
                <input type="text" name="ingredients[${rowIndex}][quantity]" class="w-24 bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md" placeholder="Qty">
                <input type="text" name="ingredients[${rowIndex}][unit]" class="w-24 bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md" placeholder="Unit">
                <button type="button" class="p-2 text-on-surface-variant hover:text-error transition-colors remove-ingredient">
                    <span class="material-symbols-outlined">close</span>
                </button>
            `;
            container.appendChild(row);
            rowIndex++;
            updateRemoveButtons();
        });

        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-ingredient');
            if (removeBtn) {
                removeBtn.closest('.ingredient-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const rows = container.querySelectorAll('.ingredient-row');
            rows.forEach((row) => {
                const removeBtn = row.querySelector('.remove-ingredient');
                if (removeBtn) {
                    removeBtn.classList.toggle('hidden', rows.length <= 1);
                }
            });
        }

        // Difficulty Segmented Buttons
        const difficultyInput = document.getElementById('difficulty');
        const difficultyBtns = document.querySelectorAll('.difficulty-btn');
        const savedDifficulty = difficultyInput.value;

        if (savedDifficulty) {
            difficultyBtns.forEach(btn => {
                if (btn.dataset.value === savedDifficulty) {
                    btn.classList.add('bg-primary-container', 'text-on-primary-container', 'border-primary-container');
                    btn.classList.remove('border-outline-variant', 'text-on-surface-variant');
                }
            });
        }

        difficultyBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const value = this.dataset.value;

                if (difficultyInput.value === value) {
                    difficultyInput.value = '';
                    difficultyBtns.forEach(b => {
                        b.classList.remove('bg-primary-container', 'text-on-primary-container', 'border-primary-container');
                        b.classList.add('border-outline-variant', 'text-on-surface-variant');
                    });
                } else {
                    difficultyInput.value = value;
                    difficultyBtns.forEach(b => {
                        b.classList.remove('bg-primary-container', 'text-on-primary-container', 'border-primary-container');
                        b.classList.add('border-outline-variant', 'text-on-surface-variant');
                    });
                    this.classList.add('bg-primary-container', 'text-on-primary-container', 'border-primary-container');
                    this.classList.remove('border-outline-variant', 'text-on-surface-variant');
                }
            });
        });

        // Loading State on Submit
        const form = document.getElementById('generation-form');
        const generateBtn = document.getElementById('generate-btn');
        const loadingOverlay = document.getElementById('loading-overlay');

        form.addEventListener('submit', function() {
            generateBtn.disabled = true;
            generateBtn.classList.add('opacity-70', 'cursor-not-allowed');
            loadingOverlay.classList.remove('hidden');
        });
    });
</script>
@endsection
