@extends('layouts.dashboard')

@section('title', 'My Recipes')

@section('content')
    {{-- Header --}}
    <section class="flex flex-col md:flex-row gap-4 md:items-end justify-between">
        <div class="max-w-2xl flex flex-col gap-1">
            <p class="font-label-md text-label-md text-tertiary">{{ $user->name }}</p>
            <h1 class="font-headline-lg text-[28px] leading-[36px] md:text-[32px] md:leading-[40px] text-on-surface">My Recipes</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">
                {{ $recipes->total() === 1 ? '1 recipe in your collection' : $recipes->total() . ' recipes in your collection' }}
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-6 md:mt-0 shrink-0">
            <a href="{{ route('generations.create') }}" class="bg-primary text-on-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-tint shadow-md transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">auto_awesome</span>
                Generate with AI
            </a>
            <a href="{{ route('recipes.create') }}" class="bg-transparent border border-primary text-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-container-low transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">add</span>
                Create Recipe
            </a>
        </div>
    </section>

    {{-- Recipe Grid --}}
    @if ($recipes->isEmpty())
        <div class="bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant/50 p-10 text-center flex flex-col items-center gap-4">
            <span class="material-symbols-outlined text-on-surface-variant/50" style="font-size: 48px;">restaurant</span>
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-2">No recipes yet</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Create your first recipe or generate one with AI to get started.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('recipes.create') }}" class="bg-primary text-on-primary font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-tint shadow-sm transition-all duration-200">
                    Create a Recipe
                </a>
                <a href="{{ route('generations.create') }}" class="bg-transparent border border-primary text-primary font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-container-low transition-all duration-200">
                    Generate with AI
                </a>
            </div>
        </div>
    @else
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($recipes as $recipe)
                <article class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 overflow-hidden flex flex-col shadow-sm hover:shadow-md transition-shadow group">
                    {{-- Image Area --}}
                    <div class="relative w-full aspect-[3/2] overflow-hidden bg-surface-container-high">
                        @if ($recipe->image_path && file_exists(public_path($recipe->image_path)))
                            <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-on-surface-variant/50">
                                <span class="material-symbols-outlined" style="font-size: 48px;">restaurant</span>
                            </div>
                        @endif

                        {{-- Status Badge --}}
                        <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-sm px-2 py-1 rounded-md border border-outline-variant/30 flex items-center gap-1 shadow-sm">
                            <span class="w-2 h-2 rounded-full {{ $recipe->statut === 'published' ? 'bg-primary' : 'bg-secondary' }}"></span>
                            <span class="font-caption text-caption text-on-surface">{{ $recipe->statut === 'published' ? 'Published' : 'Hidden' }}</span>
                        </div>

                        {{-- AI Badge --}}
                        @if ($recipe->is_ai_generated)
                            <div class="absolute top-3 right-3 bg-primary/90 text-on-primary px-2 py-1 rounded-md backdrop-blur-sm flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined" style="font-size: 14px;">auto_awesome</span>
                                <span class="font-caption text-caption">AI</span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex flex-col gap-3 flex-grow">
                        <div class="flex flex-col gap-1">
                            <h2 class="font-headline-md text-headline-md text-on-surface line-clamp-2 group-hover:text-primary transition-colors">
                                <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-primary transition-colors">{{ $recipe->title }}</a>
                            </h2>
                            @if ($recipe->description)
                                <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">{{ $recipe->description }}</p>
                            @endif
                        </div>

                        {{-- Categories --}}
                        @if ($recipe->categories->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach ($recipe->categories->take(2) as $category)
                                    <span class="bg-tertiary/10 text-tertiary-container px-2 py-0.5 rounded-full font-caption text-caption border border-tertiary/20">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Metadata --}}
                        <div class="mt-auto pt-3 border-t border-outline-variant/20 flex justify-between items-center text-on-surface-variant font-caption text-caption">
                            <div class="flex items-center gap-3">
                                @if ($recipe->prep_time)
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                                        {{ $recipe->prep_time }}m
                                    </div>
                                @endif
                                @if ($recipe->cook_time)
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">local_fire_department</span>
                                        {{ $recipe->cook_time }}m
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span>
                                {{ $recipe->created_at->format('M d, Y') }}
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('recipes.show', $recipe) }}" class="flex-1 bg-surface-container-high text-on-surface font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-container-highest transition-colors text-center">
                                View
                            </a>
                            @can('update', $recipe)
                                <a href="{{ route('recipes.edit', $recipe) }}" class="flex-1 bg-transparent border border-outline-variant/50 text-on-surface-variant font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-container-high hover:text-on-surface transition-colors text-center">
                                    Edit
                                </a>
                            @endcan
                            @can('delete', $recipe)
                                <button type="button" onclick="document.getElementById('deleteModal{{ $recipe->id }}').classList.remove('hidden')" class="bg-transparent border border-outline-variant/50 text-on-surface-variant font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-error-container hover:text-on-error-container transition-colors">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    {{-- Delete Modal --}}
                    @can('delete', $recipe)
                        <div id="deleteModal{{ $recipe->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target===this) this.classList.add('hidden')">
                            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-lg p-6 mx-4 max-w-md w-full">
                                <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Delete Recipe</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant mb-6">Are you sure you want to delete "{{ $recipe->title }}"? This action cannot be undone.</p>
                                <div class="flex gap-3 justify-end">
                                    <button type="button" onclick="document.getElementById('deleteModal{{ $recipe->id }}').classList.add('hidden')" class="bg-transparent border border-outline-variant/50 text-on-surface-variant font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-container-high transition-colors">
                                        Cancel
                                    </button>
                                    <form action="{{ route('recipes.destroy', $recipe) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-error text-on-error font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-error-container hover:text-on-error-container transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endcan
                </article>
            @endforeach
        </section>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $recipes->links() }}
        </div>
    @endif
@endsection
