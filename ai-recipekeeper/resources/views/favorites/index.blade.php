@extends('layouts.dashboard')

@section('title', 'My Favorites')

@section('content')
    {{-- Hero: Title + Search --}}
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="max-w-2xl flex flex-col gap-1">
            <h1 class="font-headline-lg text-[28px] leading-[36px] md:text-[32px] md:leading-[40px] text-on-background">Curated Favorites</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">Your personal collection of culinary masterpieces.</p>
        </div>
        <form method="GET" action="{{ route('favorites.index') }}" class="w-100%/2 md:w-1/2 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ old('search', $search) }}"
                    placeholder="Search favorites..."
                    class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                >
            </div>
        </form>
    </section>

    {{-- Category Filter Pills --}}
    @if ($categories->isNotEmpty())
        <section class="flex flex-wrap gap-2">
            @php
                $allParams = request()->query();
                unset($allParams['category']);
            @endphp

            <a
                href="{{ route('favorites.index', $allParams) }}"
                class="px-4 py-1.5 rounded-full font-label-md text-label-md transition-colors
                    {{ !request('category') ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-high text-on-surface border border-outline-variant/20 hover:bg-surface-variant' }}"
            >
                All
            </a>

            @foreach ($categories as $category)
                @php
                    $categoryParams = array_merge($allParams, ['category' => $category->id]);
                @endphp
                <a
                    href="{{ route('favorites.index', $categoryParams) }}"
                    class="px-4 py-1.5 rounded-full font-label-md text-label-md transition-colors
                        {{ request('category') == $category->id ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-high text-on-surface border border-outline-variant/20 hover:bg-surface-variant' }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach
        </section>
    @endif

    {{-- Favorites Grid --}}
    @if ($favoris->isEmpty())
        @if (request('search') || request('category'))
            {{-- Search/Filter Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center border-2 border-dashed border-outline-variant/50 rounded-2xl bg-surface-container-lowest/50 min-h-[400px]">
                <div class="w-24 h-24 mb-6 rounded-full bg-surface-container-high flex items-center justify-center text-outline">
                    <span class="material-symbols-outlined text-5xl">search_off</span>
                </div>
                <h2 class="font-headline-md text-headline-md text-on-background mb-2">No matching favorites</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-6 max-w-md">
                    Try another search or category.
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('favorites.index') }}" class="bg-primary text-on-primary font-label-md text-label-md py-2.5 px-5 rounded-lg hover:bg-surface-tint shadow-sm transition-all duration-200">
                        Clear Filters
                    </a>
                    <a href="{{ route('recipes.browse') }}" class="bg-transparent border border-primary text-primary font-label-md text-label-md py-2.5 px-5 rounded-lg hover:bg-surface-container-low transition-all duration-200">
                        Explore Recipes
                    </a>
                </div>
            </div>
        @else
            {{-- No Favorites Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center border-2 border-dashed border-outline-variant/50 rounded-2xl bg-surface-container-lowest/50 min-h-[400px]">
                <div class="w-24 h-24 mb-6 rounded-full bg-surface-container-high flex items-center justify-center text-outline">
                    <span class="material-symbols-outlined text-5xl">bookmark_border</span>
                </div>
                <h2 class="font-headline-md text-headline-md text-on-background mb-2">No favorites yet</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-6 max-w-md">
                    Your curated cookbook is empty. Start exploring recipes and tap the heart icon to save them here for easy access later.
                </p>
                <a href="{{ route('recipes.browse') }}" class="bg-primary text-on-primary font-label-md text-label-md py-3 px-6 rounded-lg hover:bg-surface-tint shadow-md transition-all duration-200 flex items-center gap-2">
                    <span class="material-symbols-outlined">explore</span>
                    Explore Recipes
                </a>
            </div>
        @endif
    @else
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($favoris as $favori)
                @php
                    $recipe = $favori->recette;
                    $ratingAvg = $recipe->avis->avg('rating');
                    $ratingAvg = $ratingAvg === null ? null : round((float) $ratingAvg, 1);
                    $ratingCount = $recipe->avis->count();
                @endphp
                <article class="bg-surface-container-lowest rounded-xl overflow-hidden border border-outline-variant/20 shadow-sm hover:shadow-md transition-all duration-300 group cursor-pointer flex flex-col h-full recipe-card-hover">
                    {{-- Image Area --}}
                    <div class="relative w-full aspect-[3/2] overflow-hidden">
                        @if ($recipe->image_path && file_exists(public_path($recipe->image_path)))
                            <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-surface-container-high text-on-surface-variant/50">
                                <span class="material-symbols-outlined" style="font-size: 48px;">restaurant</span>
                            </div>
                        @endif

                        {{-- Favorite Remove Button --}}
                        <form action="{{ route('favorites.destroy', $favori) }}" method="POST" class="absolute top-3 right-3" onclick="event.stopPropagation(); event.preventDefault(); this.submit();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 bg-surface-container-lowest/80 backdrop-blur-sm rounded-full flex items-center justify-center text-primary hover:text-error transition-colors shadow-sm" title="Remove from favorites">
                                <span class="material-symbols-outlined" style="font-size: 20px; font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </form>

                        {{-- Total Time Badge --}}
                        @php
                            $totalMinutes = ($recipe->prep_time ?? 0) + ($recipe->cook_time ?? 0);
                        @endphp
                        @if ($totalMinutes > 0)
                            <div class="absolute bottom-3 left-3">
                                <span class="px-2.5 py-1 rounded-md bg-surface-container-lowest/90 backdrop-blur-sm text-on-surface font-caption text-caption shadow-sm flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">schedule</span>
                                    @php
                                        $hours = intdiv($totalMinutes, 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                    {{ $hours > 0 ? $hours . 'h ' . $minutes . 'm' : $minutes . ' min' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex flex-col flex-grow">
                        {{-- Category Badges --}}
                        @if ($recipe->categories->isNotEmpty())
                            <div class="flex gap-2 mb-3 flex-wrap">
                                @foreach ($recipe->categories->take(2) as $category)
                                    <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-caption text-caption border border-primary/20">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Title --}}
                        <h2 class="font-headline-md text-headline-md text-on-background mb-2 group-hover:text-primary transition-colors line-clamp-2">
                            <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-primary transition-colors">{{ $recipe->title }}</a>
                        </h2>

                        {{-- Description --}}
                        @if ($recipe->description)
                            <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 mb-4 flex-grow">{{ $recipe->description }}</p>
                        @endif

                        {{-- Rating --}}
                        <div class="mt-auto pt-3 border-t border-outline-variant/20">
                            @if ($ratingCount > 0)
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center gap-0.5 text-secondary-container">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="material-symbols-outlined" style="font-size: 18px; {{ $i <= round($ratingAvg) ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                                        @endfor
                                    </div>
                                    <span class="font-caption text-caption text-on-surface-variant ml-1">{{ $ratingAvg }} ({{ $ratingCount }})</span>
                                </div>
                            @else
                                <span class="font-caption text-caption text-on-surface-variant/60">No ratings yet</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $favoris->links() }}
        </div>
    @endif

    <style>
        .recipe-card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .recipe-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(55, 102, 56, 0.1), 0 8px 10px -6px rgba(55, 102, 56, 0.1);
        }
    </style>
@endsection
