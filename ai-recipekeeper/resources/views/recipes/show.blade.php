@extends('layouts.dashboard')

@section('title', $recipe->title)

@section('content')
<div class="flex flex-col gap-8 md:gap-10">

    {{-- Hero Image --}}
    <div class="w-full aspect-[16/10] sm:aspect-[16/9] md:aspect-[16/7] rounded-xl overflow-hidden bg-surface-container-high">
        @if ($recipe->image_path && file_exists(public_path($recipe->image_path)))
            <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-on-surface-variant/50">
                <span class="material-symbols-outlined" style="font-size: 64px;">restaurant</span>
            </div>
        @endif
    </div>

    {{-- Recipe Identity --}}
    <header class="text-center max-w-3xl mx-auto flex flex-col items-center gap-3 md:gap-3.5">
        <div class="flex flex-wrap justify-center items-center gap-x-2 gap-y-1.5">
            @if ($recipe->statut === 'published')
                <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-caption text-caption border border-primary/20 flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 14px;">public</span>
                    Published
                </span>
            @else
                <span class="bg-surface-container-high text-on-surface-variant px-2.5 py-0.5 rounded-full font-caption text-caption border border-outline-variant/30 flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 14px;">visibility_off</span>
                    Hidden
                </span>
            @endif
            @if ($recipe->is_ai_generated)
                <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-caption text-caption border border-primary/20 flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 14px;">auto_awesome</span>
                    AI Generated
                </span>
            @endif
            @foreach ($recipe->categories as $category)
                <span class="bg-tertiary/10 text-tertiary-container px-2.5 py-0.5 rounded-full font-caption text-caption border border-tertiary/20">{{ $category->name }}</span>
            @endforeach
        </div>
        <h1 class="font-display-lg text-[28px] leading-[36px] md:text-[40px] md:leading-[48px] lg:text-[44px] lg:leading-[52px] text-on-surface font-bold tracking-tight">{{ $recipe->title }}</h1>
        <p class="font-label-md text-label-md text-on-surface-variant">
            By {{ $recipe->user->name }} &middot; {{ $recipe->created_at->format('M d, Y') }}
        </p>
        @if ($recipe->description)
            <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed max-w-2xl mt-1 md:mt-2">{{ $recipe->description }}</p>
        @endif
    </header>

    {{-- Metadata --}}
    <div class="flex flex-wrap justify-center items-center gap-x-7 gap-y-4 border-y border-outline-variant/20 py-4">
        @if ($recipe->prep_time)
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-primary" style="font-size: 22px;">schedule</span>
                <div class="flex flex-col items-start leading-tight">
                    <span class="font-caption text-caption text-on-surface-variant">Prep</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $recipe->prep_time }} min</span>
                </div>
            </div>
        @endif
        @if ($recipe->cook_time)
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-primary" style="font-size: 22px;">oven_gen</span>
                <div class="flex flex-col items-start leading-tight">
                    <span class="font-caption text-caption text-on-surface-variant">Cook</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $recipe->cook_time }} min</span>
                </div>
            </div>
        @endif
        @if ($recipe->servings)
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-primary" style="font-size: 22px;">restaurant</span>
                <div class="flex flex-col items-start leading-tight">
                    <span class="font-caption text-caption text-on-surface-variant">Servings</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $recipe->servings }}</span>
                </div>
            </div>
        @endif
        @if ($recipe->difficulty)
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-primary" style="font-size: 22px;">trending_up</span>
                <div class="flex flex-col items-start leading-tight">
                    <span class="font-caption text-caption text-on-surface-variant">Difficulty</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $recipe->difficulty }}</span>
                </div>
            </div>
        @endif
        @if ($ratingAvg !== null)
            <div class="flex items-center gap-2.5">
                <span class="flex items-center text-secondary" role="img" aria-label="{{ $ratingAvg }} out of 5 stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined" style="font-size: 18px; {{ $i <= round($ratingAvg) ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                    @endfor
                </span>
                <div class="flex flex-col items-start leading-tight">
                    <span class="font-caption text-caption text-on-surface-variant">Rating</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold">{{ $ratingAvg }}/5 <span class="font-muted font-caption text-caption text-on-surface-variant font-normal">({{ $ratingCount }})</span></span>
                </div>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row flex-wrap justify-center items-stretch gap-3">
        @if ($favorite)
            <form action="{{ route('favorites.destroy', $favorite) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-surface-tint shadow-sm transition-colors">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    Remove from Favorites
                </button>
            </form>
        @else
            <form action="{{ route('favorites.store') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <input type="hidden" name="recette_id" value="{{ $recipe->id }}">
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-surface-tint shadow-sm transition-colors">
                    <span class="material-symbols-outlined">favorite</span>
                    Add to Favorites
                </button>
            </form>
        @endif

        @can('update', $recipe)
            <a href="{{ route('recipes.edit', $recipe) }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-primary text-primary hover:bg-primary-container hover:text-on-primary-container transition-colors font-label-md text-label-md">
                <span class="material-symbols-outlined">edit</span>
                Edit
            </a>
        @endcan

        @can('delete', $recipe)
            <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Are you sure you want to delete &quot;{{ $recipe->title }}&quot;? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg text-error font-label-md text-label-md border border-error/40 hover:bg-error-container/40 hover:border-error transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                    Delete
                </button>
            </form>
        @endcan
    </div>

    {{-- Main Cooking Area --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-10 items-start border-t border-outline-variant/20 pt-8 md:pt-10">

        {{-- Ingredients --}}
        @if ($recipe->ingredients->isNotEmpty())
            <aside class="md:col-span-4 md:sticky top-24 h-fit bg-surface-container-low rounded-xl p-6 md:p-7 border border-outline-variant/20">
                <h2 class="font-headline-md text-headline-md text-primary mb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined">kitchen</span>
                    Ingredients
                </h2>
                <p class="font-caption text-caption text-on-surface-variant mb-4">{{ $recipe->ingredients->count() }} {{ Str::plural('item', $recipe->ingredients->count()) }}</p>
                <ul class="font-body-md text-body-md">
                    @foreach ($recipe->ingredients as $ingredient)
                        <li class="flex items-baseline justify-between gap-4 py-3 border-b border-outline-variant/10 last:border-b-0">
                            <span class="font-medium text-on-surface whitespace-nowrap shrink-0">
                                @if ($ingredient->pivot->quantity || $ingredient->pivot->unit)
                                    {{ $ingredient->pivot->quantity ?? '' }}@if ($ingredient->pivot->unit) {{ $ingredient->pivot->unit }}@endif
                                @endif
                            </span>
                            <span class="text-right text-on-surface-variant min-w-0 break-words">{{ $ingredient->name }}</span>
                        </li>
                    @endforeach
                </ul>
            </aside>
        @endif

        {{-- Steps --}}
        <div class="{{ $recipe->ingredients->isNotEmpty() ? 'md:col-span-8' : 'md:col-span-12' }}">
            @if ($recipe->etapes->isNotEmpty())
                <h2 class="font-headline-md text-headline-md text-primary mb-7 flex items-center gap-2">
                    <span class="material-symbols-outlined">format_list_numbered</span>
                    Preparation Steps
                </h2>
                <ol class="font-body-lg text-body-lg">
                    @foreach ($recipe->etapes->sortBy('step_number') as $etape)
                        <li class="relative flex gap-4 @if (!$loop->last) pb-8 @endif">
                            <span class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-label-md text-label-md font-semibold shadow-sm">{{ sprintf('%02d', $etape->step_number) }}</span>
                            @if (!$loop->last)
                                <span class="absolute left-5 top-10 bottom-8 w-px bg-outline-variant/40" aria-hidden="true"></span>
                            @endif
                            <p class="pt-1.5 min-w-0 text-on-surface leading-relaxed">{{ $etape->instruction }}</p>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

    </div>

    {{-- Reviews --}}
    <section class="border-t border-outline-variant/30 pt-10 md:pt-12 scroll-mt-24">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <h2 class="font-headline-md text-headline-md text-on-surface">Reviews</h2>
            @if ($ratingAvg !== null)
                <div class="flex flex-col items-start md:items-end gap-1.5">
                    <span class="flex items-center gap-1 text-secondary" role="img" aria-label="{{ $ratingAvg }} out of 5 stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="material-symbols-outlined" style="font-size: 22px; {{ $i <= round($ratingAvg) ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                        @endfor
                    </span>
                    <span class="flex items-baseline gap-2">
                        <span class="font-headline-md text-headline-md text-on-surface">{{ $ratingAvg }}/5</span>
                        <span class="font-body-md text-body-md text-on-surface-variant">{{ $ratingCount }} {{ Str::plural('review', $ratingCount) }}</span>
                    </span>
                </div>
            @else
                <span class="font-label-md text-label-md text-on-surface-variant">No reviews yet</span>
            @endif
        </div>

        @if ($userReview)
            {{-- Own Review --}}
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 mb-6">
                <div class="flex flex-wrap justify-between items-start gap-3 mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <strong class="font-label-md text-label-md text-on-surface">Your review</strong>
                            <span class="font-caption text-caption text-on-surface-variant">&middot; {{ $userReview->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-secondary mb-2" role="img" aria-label="{{ $userReview->rating }} out of 5 stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined" style="font-size: 18px; {{ $i <= $userReview->rating ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                            @endfor
                        </div>
                        @if ($userReview->comment)
                            <p class="font-body-md text-body-md text-on-surface-variant">{{ $userReview->comment }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" id="edit-review-toggle" aria-expanded="false" aria-controls="edit-review-form" class="flex items-center gap-1 px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary-container hover:text-on-primary-container transition-colors font-label-md text-label-md">
                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                            Edit Review
                        </button>
                        <form action="{{ route('reviews.destroy', $userReview) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-1 px-3 py-2 rounded-lg text-error hover:bg-error-container/40 transition-colors font-label-md text-label-md">
                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div id="edit-review-form" class="hidden border-t border-outline-variant/20 pt-5">
                    <form action="{{ route('reviews.update', $userReview) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-1">
                                <label for="edit-rating" class="font-label-md text-label-md text-on-surface-variant block mb-2">Rating</label>
                                <select name="rating" id="edit-rating" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" @selected($userReview->rating === $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('rating')
                                    <div class="text-error text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label for="edit-comment" class="font-label-md text-label-md text-on-surface-variant block mb-2">Comment</label>
                                <textarea name="comment" id="edit-comment" rows="2" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md resize-none">{{ old('comment', $userReview->comment) }}</textarea>
                                @error('comment')
                                    <div class="text-error text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="mt-4 bg-primary text-on-primary font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-tint shadow-sm transition-all">Update Review</button>
                    </form>
                </div>
            </div>
        @else
            {{-- Review Form --}}
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 mb-6">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-1">Share your experience</h3>
                <p class="font-body-md text-body-md text-on-surface-variant mb-4">Rate the recipe and tell others how it turned out.</p>
                <form action="{{ route('reviews.store', $recipe) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <label for="rating" class="font-label-md text-label-md text-on-surface-variant block mb-2">Rating</label>
                            <select name="rating" id="rating" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            @error('rating')
                                <div class="text-error text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="md:col-span-3">
                            <label for="comment" class="font-label-md text-label-md text-on-surface-variant block mb-2">Comment</label>
                            <textarea name="comment" id="comment" rows="2" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md resize-none" placeholder="Share your thoughts (optional)"></textarea>
                            @error('comment')
                                <div class="text-error text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-primary text-on-primary font-label-md text-label-md py-2 px-4 rounded-lg hover:bg-surface-tint shadow-sm transition-all">Submit Review</button>
                </form>
            </div>
        @endif

        {{-- Other Reviews --}}
        @if ($recipe->avis->count() > ($userReview ? 1 : 0))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($recipe->avis as $review)
                    @if ($userReview && $review->id === $userReview->id)
                        @continue
                    @endif
                    <article class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/20">
                        <div class="flex justify-between items-start gap-3 mb-2">
                            <div>
                                <strong class="font-label-md text-label-md text-on-surface">{{ $review->user->name }}</strong>
                                <span class="font-caption text-caption text-on-surface-variant">&middot; {{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                            @can('delete', $review)
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-error hover:bg-error-container/40 p-1 rounded transition-colors" title="Delete review">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                    </button>
                                </form>
                            @endcan
                        </div>
                        <div class="flex items-center gap-1 text-secondary mb-2" role="img" aria-label="{{ $review->rating }} out of 5 stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined" style="font-size: 18px; {{ $i <= $review->rating ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                            @endfor
                        </div>
                        @if ($review->comment)
                            <p class="font-body-md text-body-md text-on-surface-variant">{{ $review->comment }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('edit-review-toggle');
        const form = document.getElementById('edit-review-form');

        if (toggle && form) {
            toggle.addEventListener('click', function () {
                const isHidden = form.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!isHidden));

                if (!isHidden) {
                    const firstField = form.querySelector('select, textarea, input');
                    if (firstField) firstField.focus();
                }
            });
        }
    });
</script>
@endsection