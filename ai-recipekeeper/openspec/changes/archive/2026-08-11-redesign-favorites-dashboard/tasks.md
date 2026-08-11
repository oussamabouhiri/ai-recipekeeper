## 1. Controller Enhancement

- [x] 1.1 Update `FavoriWebController::index()` to accept `search` and `category` query parameters from the request
- [x] 1.2 Add server-side search using Eloquent `whereHas('recette', ...)` with LIKE clauses on `title` and `description`
- [x] 1.3 Add server-side category filtering using `whereHas('recette.categories', ...)` scoped to the category ID parameter
- [x] 1.4 Add eager loading for `recette.avis` alongside existing `recette.user` and `recette.categories`
- [x] 1.5 Add `withQueryString()` to the pagination call to preserve search and filter parameters across pages
- [x] 1.6 Add a categories query that fetches only categories present in the authenticated user's favorited recipes, ordered by name
- [x] 1.7 Pass `$search`, `$categories`, and `$favoris` variables to the view

## 2. View Rewrite

- [x] 2.1 Replace `@extends('layouts.app')` with `@extends('layouts.dashboard')` in `resources/views/favorites/index.blade.php`
- [x] 2.2 Create the page header section with "Curated Favorites" heading (responsive: `display-lg` on desktop, `headline-lg-mobile` on mobile) and subtitle
- [x] 2.3 Create the search form with a GET method, `search` input field, and search icon, matching the Browse page pattern
- [x] 2.4 Create the category filter chips section with "All" chip (always active when no category selected) and dynamic category chips from the controller
- [x] 2.5 Implement active state styling on category chips using `request('category')` comparison, matching Browse page pattern
- [x] 2.6 Create the empty state section with bookmark icon, "No favorites yet" heading, helpful copy, and "Explore Recipes" CTA linking to `route('recipes.browse')`
- [x] 2.7 Create the responsive recipe card grid: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6`
- [x] 2.8 Create the recipe card component with image area (`aspect-[3/2]`), image/fallback display, and heart/remove button overlay
- [x] 2.9 Implement the heart/remove button as a form with `@csrf`, `@method('DELETE')`, `onclick="event.stopPropagation(); event.preventDefault(); this.submit();"` pattern from Browse
- [x] 2.10 Add recipe metadata to card: category badges, title (linked to `route('recipes.show', $favori->recette)`), description (line-clamp-2), prep time, cook time, servings, difficulty
- [x] 2.11 Add real rating display: compute `$favori->recette->avis->avg('rating')` and `$favori->recette->avis->count()` in Blade, show stars and count only when reviews exist
- [x] 2.12 Add pagination links with `{{ $favoris->links() }}` below the grid
- [x] 2.13 Handle search-no-results empty state: show "No favorites found" with "Clear Filters" link when search/filter returns empty but user has favorites

## 3. Navigation Active State

- [x] 3.1 Update the desktop nav "My Favorites" link in `layouts/dashboard.blade.php` to apply active styling (`text-primary font-semibold border-b-2 border-primary pb-1`) when `request()->routeIs('favorites.index')`
- [x] 3.2 Update the mobile bottom nav "Favs" link in `layouts/dashboard.blade.php` to apply active styling (`bg-primary-container text-on-primary-container` or similar) when `request()->routeIs('favorites.index')`

## 4. Verification

- [x] 4.1 Run existing `FavoritesBladeTest` to confirm all tests pass (guest redirect, shows recipes, empty state, add/remove)
- [x] 4.2 Run existing `FavoritesCrudTest` to confirm backend CRUD behavior is unchanged
- [x] 4.3 Run existing `FavoritesAuthorizationTest` to confirm authorization is unaffected
- [x] 4.4 Run existing `FavoritesValidationTest` to confirm validation is unaffected
- [ ] 4.5 Manually verify: search filters only the authenticated user's favorites
- [ ] 4.6 Manually verify: category filter shows only categories from user's favorites
- [ ] 4.7 Manually verify: heart button removes favorite via server-side form submission
- [ ] 4.8 Manually verify: clicking card navigates to correct recipe detail page
- [ ] 4.9 Manually verify: responsive layout (1 column mobile, 2 tablet, 3 desktop)
- [ ] 4.10 Manually verify: pagination preserves search and category parameters
- [ ] 4.11 Manually verify: active navigation state on desktop and mobile
- [ ] 4.12 Manually verify: empty state shows "Explore Recipes" linking to `/browse`
