@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome Hero --}}
    <section class="flex flex-col md:flex-row gap-4 md:items-end justify-between">
        <div class="max-w-2xl flex flex-col gap-1">
            <p class="font-label-md text-label-md text-tertiary">{{ $greeting }}, {{ $user->name }}</p>
            <h1 class="font-headline-lg text-[28px] leading-[36px] md:text-[32px] md:leading-[40px] text-on-surface">What are we cooking today?</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Your personalized culinary workspace. Generate new ideas, organize your favorites, or dive into a recipe.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-6 md:mt-0 shrink-0">
            <a href="{{ route('generations.create') }}" class="bg-primary text-on-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-tint shadow-md transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">auto_awesome</span>
                Generate with AI
            </a>
            <a href="{{ route('recipes.create') }}" class="bg-transparent border border-primary text-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-container-low transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">add</span>
                Create Manual Recipe
            </a>
        </div>
    </section>

    {{-- Featured Recipe + AI Inspiration --}}
    <section class="grid grid-cols-1 md:grid-cols-12 gap-4">
        @if ($featured)
            <a href="{{ route('recipes.show', $featured) }}" class="md:col-span-8 bg-surface-container-lowest rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm flex flex-col sm:flex-row hover:shadow-md transition-shadow group">
                <div class="sm:w-2/5 h-48 sm:h-auto shrink-0 relative overflow-hidden bg-surface-container-high">
                    @if ($featured->image_path && file_exists(public_path($featured->image_path)))
                        <img src="{{ asset($featured->image_path) }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-on-surface-variant/50">
                            <span class="material-symbols-outlined" style="font-size: 48px;">restaurant</span>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 bg-primary/90 text-on-primary px-3 py-1 rounded-full font-caption text-caption flex items-center gap-1 backdrop-blur-sm shadow-sm">
                        <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                        Featured Recipe
                    </span>
                </div>
                <div class="p-6 flex flex-col justify-between flex-grow">
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            @if ($featured->categories->isNotEmpty())
                                <span class="bg-tertiary/10 text-tertiary-container px-2 py-0.5 rounded-full font-caption text-caption border border-tertiary/20">{{ $featured->categories->first()->name }}</span>
                            @endif
                            @if (($featured->prep_time ?? 0) + ($featured->cook_time ?? 0) > 0)
                                <span class="text-on-surface-variant font-caption text-caption flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                    @php
                                        $totalMinutes = ($featured->prep_time ?? 0) + ($featured->cook_time ?? 0);
                                        $hours = intdiv($totalMinutes, 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                    {{ $hours > 0 ? $hours . 'h ' . $minutes . 'm' : $minutes . 'm' }}
                                </span>
                            @endif
                            @if ($featured->difficulty)
                                <span class="text-on-surface-variant font-caption text-caption flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">signal_cellular_alt</span>
                                    {{ $featured->difficulty }}
                                </span>
                            @endif
                            @if ($featured->is_ai_generated)
                                <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-full font-caption text-caption border border-primary/20 flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">auto_awesome</span>
                                    AI Generated
                                </span>
                            @endif
                        </div>
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-2 group-hover:text-primary transition-colors">{{ $featured->title }}</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">{{ $featured->description }}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-outline-variant/20 flex items-center justify-between">
                        <div class="flex items-center gap-1 text-secondary font-label-md text-label-md" data-rating-avg="{{ $ratingAvg ?? '' }}" data-rating-count="{{ $ratingCount }}">
                            @if ($ratingCount > 0)
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined" style="font-size: 18px; {{ $i <= round($ratingAvg) ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                                @endfor
                                <span class="text-on-surface-variant ml-1">({{ $ratingCount }} reviews)</span>
                            @else
                                <span class="text-on-surface-variant font-label-md text-label-md">No reviews yet</span>
                            @endif
                        </div>
                        <span class="text-primary p-2" aria-hidden="true">
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </span>
                    </div>
                </div>
            </a>
        @else
            <a href="{{ route('recipes.create') }}" class="md:col-span-8 bg-surface-container-lowest rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm flex flex-col items-center justify-center text-center p-10 gap-4 hover:shadow-md transition-shadow">
                <span class="material-symbols-outlined text-on-surface-variant/50" style="font-size: 48px;">restaurant</span>
                <div>
                    <h2 class="font-headline-md text-headline-md text-on-surface mb-2">No recipes yet</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Create your first recipe or generate one with AI.</p>
                </div>
                <span class="bg-primary text-on-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-tint shadow-md transition-all duration-200">Create a Recipe</span>
            </a>
        @endif

        <div class="md:col-span-4 flex flex-col gap-4">
            <div class="bg-surface-container-high rounded-xl p-6 flex flex-col h-full justify-between shadow-sm border border-outline-variant/10">
                <div>
                    <div class="flex items-center gap-2 text-primary mb-3">
                        <span class="material-symbols-outlined">psychology</span>
                        <h3 class="font-label-md text-label-md font-bold">Need Inspiration?</h3>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-4">Tell the AI what's in your fridge, or let it surprise you based on your flavor profile.</p>
                </div>
                <a href="{{ route('generations.create') }}" class="bg-primary text-on-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-tint shadow-md transition-all duration-200 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">auto_awesome</span>
                    Open the AI Generator
                </a>
            </div>
        </div>
    </section>

    {{-- My Favorites --}}
    <section class="flex flex-col gap-5">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">My Favorites</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    {{ $favoritesCount === 1 ? '1 recipe saved' : $favoritesCount . ' recipes saved' }}
                </p>
            </div>
            <a href="{{ route('favorites.index') }}" class="font-label-md text-label-md text-primary hover:text-primary-container transition-colors flex items-center gap-1">
                View all
                <span class="material-symbols-outlined" style="font-size: 16px;">chevron_right</span>
            </a>
        </div>

        @if ($favoriteRecipes->isEmpty())
            <div class="bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant/50 p-10 text-center flex flex-col items-center gap-4">
                <span class="material-symbols-outlined text-on-surface-variant/50" style="font-size: 40px;">favorite_border</span>
                <p class="font-body-md text-body-md text-on-surface-variant">You haven't saved any favorites yet.</p>
                <a href="{{ route('recipes.index') }}" class="bg-primary text-on-primary font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-tint shadow-sm transition-all duration-200">Browse Recipes</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($favoriteRecipes as $favori)
                    <a href="{{ route('recipes.show', $favori->recette) }}" class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                        <div class="h-32 relative overflow-hidden bg-surface-container-high">
                            @if ($favori->recette->image_path && file_exists(public_path($favori->recette->image_path)))
                                <img src="{{ asset($favori->recette->image_path) }}" alt="{{ $favori->recette->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-on-surface-variant/50">
                                    <span class="material-symbols-outlined" style="font-size: 32px;">restaurant</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-label-md text-label-md font-semibold text-on-surface group-hover:text-primary transition-colors line-clamp-1">{{ $favori->recette->title }}</h3>
                            @if ($favori->recette->categories->isNotEmpty())
                                <p class="font-caption text-caption text-on-surface-variant mt-1">{{ $favori->recette->categories->first()->name }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
