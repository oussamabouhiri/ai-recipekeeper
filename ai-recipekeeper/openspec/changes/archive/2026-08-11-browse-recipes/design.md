## Context

The application has two coexisting design systems: a modern Tailwind CSS v4 / Material Design 3 system (used by the dashboard and auth pages) and a legacy Bootstrap 5 system (used by recipe CRUD, favorites, and admin pages). The browse feature will use the modern system exclusively, consistent with the dashboard layout.

Key existing infrastructure to reuse:
- `Recette::scopeVisibleTo($user)` for visibility rules
- `Category` model with `belongsToMany(Recette)` relationship
- `Favori` model with `belongsToMany` through pivot, plus existing POST/DELETE web routes
- `layouts/dashboard.blade.php` with desktop top nav and mobile bottom nav
- Material Symbols Outlined icon set
- Tailwind v4 theme tokens (primary, surface, outline, etc.)
- Laravel `->paginate()` with `withQueryString()`

No new database tables, migrations, or model changes are needed. All required data fields already exist on the `Recette` model and its relationships.

## Goals / Non-Goals

**Goals:**
- Provide a visual recipe discovery experience using recipe cards in a responsive grid
- Implement server-side search (title + description) and category filtering
- Allow authenticated users to favorite/unfavorite recipes directly from browse cards
- Use the existing dashboard layout and Tailwind/MD3 design system
- Maintain existing behavior for all other routes and features
- Follow the project's Laravel conventions (Form Requests not needed for read-only queries, policies for authorization, Blade templates)

**Non-Goals:**
- AJAX/JavaScript-based favorite toggling (server-side form POST is sufficient for this phase)
- New database fields (no cuisine_type, dietary flags, tags, or rating fields)
- Client-side search with debouncing (server-side LIKE queries with form submission)
- Infinite scrolling (standard pagination is sufficient)
- Modifying the existing `/recipes` page or its behavior
- Modifying the existing API endpoints
- Adding new API endpoints for search/filter
- Dark mode support
- Multi-category filtering (single category filter only)
- Difficulty filtering (can be added later as a low-risk enhancement)

## Decisions

### Decision 1: New `/browse` route vs. modifying `/recipes`

**Choice:** Create a new `GET /browse` route with a dedicated `browse()` method on `RecipeWebController`.

**Rationale:** The existing `/recipes` page serves as a management view (table with edit/delete actions, status badges, policy-gated buttons). The browse page serves a fundamentally different purpose: discovery. Separating them avoids complicating the existing controller logic, keeps the management view intact, and allows the browse page to use a different layout (`layouts/dashboard.blade.php`) without affecting other recipe pages.

**Alternatives considered:**
- Modifying `RecipeWebController@index` to support both table and card views via query parameter: rejected because it would mix concerns and require conditional layout switching.
- Creating a separate `BrowseController`: rejected because browse is conceptually a recipe listing feature and belongs on the existing recipe controller.

### Decision 2: Layout selection

**Choice:** Use `layouts/dashboard.blade.php` (Tailwind v4 / MD3).

**Rationale:** The dashboard layout is the modern, polished design system with mobile bottom nav, desktop top nav, footer, and the Material Design 3 color tokens. All new user-facing pages should use this layout for consistency. The Bootstrap `layouts/app.blade.php` is legacy and should not be used for new pages.

**Alternatives considered:**
- Creating a new dedicated layout: rejected because the dashboard layout already provides everything needed (nav, footer, mobile bottom bar, Tailwind theme).
- Using `layouts/app.blade.php`: rejected because it lacks mobile bottom nav, uses Bootstrap instead of Tailwind, and is visually inconsistent with the dashboard.

### Decision 3: Server-side search/filter architecture

**Choice:** Use Laravel query builder methods directly in the controller, with GET parameters for search and category.

**Rationale:** The existing codebase uses Eloquent queries directly in controllers (no service layer, no query builder classes). Adding a search service or filter class would be over-engineering for a single-page search with two filter dimensions. The controller method stays focused and readable.

**Query structure:**
```php
Recette::query()
    ->visibleTo($user)
    ->with(['categories', 'favoris'])
    ->when($search, fn($q, $s) => $q->where(fn($w) => 
        $w->where('title', 'like', "%{$s}%")
          ->orWhere('description', 'like', "%{$s}%")
    ))
    ->when($categoryId, fn($q, $id) => $q->whereHas('categories', fn($c) => 
        $c->where('categories.id', $id)
    ))
    ->latest()
    ->paginate(12)
    ->withQueryString();
```

**Alternatives considered:**
- Spatie Laravel Query Builder: rejected because it's a new dependency for simple filtering.
- Dedicated SearchService class: rejected because the query is simple enough for the controller.
- Client-side filtering with JavaScript: rejected because the app is Blade-based with no JS framework.

### Decision 4: Favorite state on cards

**Choice:** Pre-compute favorite IDs in the controller using `$user->favoris()->pluck('recette_id')` and pass as a collection to the view. Use `@if($favoriteIds->contains($recipe->id))` in Blade.

**Rationale:** This avoids N+1 queries (one query for all favorites instead of per-card). The existing favoris system uses a simple pivot table with `user_id` and `recette_id`, so `pluck()` is efficient. The favorite toggle uses the existing form POST/DELETE mechanism.

**Alternatives considered:**
- Eager-loading `favoris` on each recipe and checking in Blade: rejected because it loads full Favori model data when only IDs are needed.
- Adding `is_favorited` computed attribute to Recette model: rejected because it would be user-specific and model-level, which breaks separation of concerns.

### Decision 5: Favorite toggle redirect behavior

**Choice:** After favorite add/remove, redirect back to `/browse` preserving current query parameters (search, category, page).

**Rationale:** The existing favorites system uses `redirect()->back()`. For the browse page, we need to preserve the current filter state so the user doesn't lose their place. Laravel's `redirect()->back()->withInput()` or explicit URL reconstruction with current query params achieves this.

**Implementation:** The favorite form action URL will include hidden fields or the redirect will reconstruct the URL from the current request's query parameters.

### Decision 6: Pagination size

**Choice:** 12 recipes per page.

**Rationale:** 12 divides evenly into 1, 2, 3, and 4 column layouts, so the last row is always full regardless of viewport width. This provides a clean visual grid on all breakpoints.

### Decision 7: Category chip behavior

**Choice:** "All" always appears first, followed by all categories from the database ordered by name. The active chip is visually distinguished using the Tailwind primary color tokens.

**Rationale:** The existing category names are in French (Entrée, Plat principal, Dessert, etc.) and should be displayed as-is. Ordering alphabetically makes the list predictable and scannable.

### Decision 8: Difficulty display mapping

**Choice:** Display difficulty values as-is from the database (Facile, Moyen, Difficile) since the existing UI already uses these values.

**Rationale:** The seeder stores French values. The existing `recipes/create.blade.php` form uses English labels (Easy/Medium/Hard) in the select dropdown, but the stored values are French. For consistency with the stored data, display the actual values. The dashboard already displays difficulty without mapping.

## Risks / Trade-offs

- **[Risk] LIKE queries on large datasets** → Mitigation: With standard Laravel pagination (12 per page), the query remains efficient for collections under ~10,000 recipes. If the collection grows significantly, a full-text index can be added later. For now, LIKE is sufficient.

- **[Risk] Search term in URL may be user-unfriendly** → Mitigation: Use `withQueryString()` so pagination links preserve parameters. The URL format `?search=risotto&category=3&page=2` is readable and shareable.

- **[Risk] Favorite toggle causes full page reload** → Mitigation: Acceptable for this phase. The page reload is fast (server-rendered, no heavy JS). AJAX toggle can be added as a future enhancement without changing the spec.

- **[Risk] Category names are in French** → Mitigation: Display as-is from the database. No localization layer exists in the app. Adding one is out of scope.

- **[Risk] Image_path is always null on existing recipes** → Mitigation: All cards will show the restaurant icon fallback initially. This is consistent with the dashboard's existing fallback behavior. When images are added to recipes, they will automatically appear.

- **[Trade-off] No difficulty filter in initial implementation** → Accepted because it can be added as a trivial enhancement later (one additional `->when()` clause) without any architectural changes.

- **[Trade-off] No multi-category filtering** → Accepted because single-category filtering covers the primary use case and keeps the UI simple. Multi-category can be added later if needed.

## Migration Plan

No migration is needed. The feature is additive:
1. Add the `browse` method to `RecipeWebController`
2. Add the `GET /browse` route
3. Create `resources/views/recipes/browse.blade.php`
4. Update navigation links in `layouts/dashboard.blade.php`
5. Create `tests/Feature/BrowseRecipesTest.php`
6. Run full test suite to verify no regressions

**Rollback:** Remove the new route, controller method, view, and test file. No database rollback needed.

## Open Questions

None. All design decisions are resolved with clear rationale based on the existing codebase patterns.
