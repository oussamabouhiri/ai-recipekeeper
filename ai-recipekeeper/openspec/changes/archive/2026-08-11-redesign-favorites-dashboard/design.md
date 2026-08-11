## Context

The Favorites page (`GET /favorites`) currently uses the legacy Bootstrap layout (`layouts.app`) with a table-based UI. The rest of the application — Browse, Dashboard, My Recipes — has been migrated to the Tailwind/MD3 design system using `layouts.dashboard.blade.php`. The Favorites page is the last remaining page on the old design system.

The existing `FavoriWebController::index()` method performs a simple query: `$request->user()->favoris()->with('recette.user', 'recette.categories')->latest()->paginate()`. It supports no search, no filtering, and does not eager-load reviews for rating computation.

The Browse page (`recipes/browse.blade.php`) has already established patterns for search forms, category filter chips, recipe cards, image handling, favorite toggling, and pagination that should be reused.

## Goals / Non-Goals

**Goals:**
- Migrate the Favorites page to `layouts.dashboard.blade.php` with Tailwind/MD3 styling
- Add server-side search filtering by recipe title and description
- Add server-side category filtering with dynamically loaded category chips
- Display favorites as recipe cards in a responsive grid (1/2/3 columns)
- Show real recipe images with fallback, heart/remove button, category badges, metadata, and real ratings
- Preserve search and filter state through pagination
- Add active navigation state for the Favorites page
- Maintain all existing backend behavior (add, remove, authorization, cascade)

**Non-Goals:**
- Changing the Favori model or database schema
- Adding new routes (existing routes are sufficient)
- Modifying other pages (Browse, Dashboard, etc.)
- Implementing client-side filtering or AJAX favorite toggling
- Adding new API endpoints
- Changing the favorite add/remove mechanism

## Decisions

### Decision 1: Reuse `layouts.dashboard.blade.php` as-is

**Choice:** Extend `layouts.dashboard` and use its `@section('content')` pattern.

**Rationale:** This is the same approach used by `browse.blade.php` and `dashboard.blade.php`. The layout already provides:
- Desktop header with navigation
- Mobile bottom navigation
- Footer
- Tailwind CSS + Material Symbols
- Playfair Display + Inter fonts
- Success flash message handling

**Alternative considered:** Creating a new layout — rejected because the existing layout already provides everything needed and creating a second layout would violate DRY.

### Decision 2: Server-side search and filtering via query parameters

**Choice:** Use GET form submission with `search` and `category` query parameters, processed by the controller using Eloquent `when()` clauses.

**Rationale:** This matches the exact pattern already established by `browse.blade.php` and `RecipeWebController::browse()`. Using query parameters:
- Preserves state through pagination via `withQueryString()`
- Allows bookmarking/filtering URLs
- Works without JavaScript
- Is consistent with the existing codebase

**Alternative considered:** Client-side filtering with JavaScript — rejected because the project uses server-side rendering with Blade and the user specified no client-side filtering.

### Decision 3: Category chips scoped to user's favorites

**Choice:** Query categories that exist in the authenticated user's favorited recipes only, not all categories in the database.

**Rationale:** Showing all categories would be misleading — if a user has no desserts in their favorites, showing a "Dessert" filter chip that returns zero results is poor UX. The Browse page shows all categories because it searches all published recipes, but Favorites is a personal subset.

**Implementation:**
```php
$categories = Category::whereHas('recettes.favoris', fn ($q) => $q
    ->where('user_id', $request->user()->id)
)->orderBy('name')->get();
```

### Decision 4: Eager-load avis for rating computation

**Choice:** Add `'recette.avis'` to the eager loading chain and compute ratings in the Blade template.

**Rationale:** Ratings are computed from the `avis` table using `avg('rating')` and `count()`. Without eager loading, this would cause N+1 queries. The DashboardController already uses this pattern. Computing in Blade (rather than adding virtual attributes to the model) keeps the approach simple and avoids model changes.

### Decision 5: Heart button uses existing form submission

**Choice:** Each card's heart button uses a `<form>` with `@csrf` and `@method('DELETE')` that submits to `favorites.destroy`, with `event.stopPropagation()` to prevent card navigation.

**Rationale:** This is the exact pattern used by `browse.blade.php` for the favorite toggle. It uses the existing server-side endpoint without JavaScript state management. The `onclick="event.stopPropagation(); event.preventDefault(); this.submit();"` pattern is already proven in the codebase.

### Decision 6: No changes to routes or models

**Choice:** Keep all existing routes and models unchanged.

**Rationale:** The existing routes (`GET /favorites`, `POST /favorites`, `DELETE /favorites/{favori}`) are sufficient. The search and filtering are handled via query parameters on the existing `GET /favorites` route. The Favori model and its relationships are already correct.

## Risks / Trade-offs

- **[Risk] Favorites page may feel slow with large datasets** → Mitigation: Pagination (12 per page) limits query size. Eager loading prevents N+1. The `latest()` ordering ensures most recent favorites appear first.

- **[Risk] Category chips may show many items if user has favorites in many categories** → Mitigation: Horizontal scrolling with `flex-wrap` on desktop, limited by the number of categories in the database (currently ~12).

- **[Risk] Search may not find recipes with non-standard characters** → Mitigation: SQLite LIKE is case-insensitive for ASCII. MySQL LIKE is case-insensitive by default with utf8 collation. The existing Browse page uses the same approach without issues.

- **[Trade-off] No AJAX favorite removal** → The page reloads after removing a favorite. This is consistent with the existing behavior on Browse and is the standard pattern in this codebase. AJAX would require JavaScript the project doesn't use for this purpose.

- **[Trade-off] Ratings may show decimal averages (e.g., 4.3)** → This matches the Dashboard's existing rating display pattern and is more informative than rounded integers.
