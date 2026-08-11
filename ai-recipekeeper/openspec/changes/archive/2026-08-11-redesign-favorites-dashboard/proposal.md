## Why

The current My Favorites page (`GET /favorites`) uses the legacy Bootstrap layout (`layouts.app`) and renders favorites as a plain HTML table — no recipe images, no search, no category filters, no metadata, no modern design. Meanwhile, the Browse page and Dashboard have already been redesigned with the Tailwind/MD3 design system using `layouts.dashboard`. The Favorites page is now the only major page still on the old design system, creating an inconsistent user experience.

Additionally, the Favorites page lacks search and category filtering — features that already exist on Browse. As users accumulate more favorites, finding a specific recipe becomes difficult without these tools.

## What Changes

- **Controller enhancement**: Add server-side search (by title and description) and category filtering to `FavoriWebController::index()`, scoped to the authenticated user's favorites only
- **View rewrite**: Replace the Bootstrap table layout with a Tailwind/MD3 recipe card grid, using `layouts.dashboard` as the layout
- **Search UI**: Add a search input with server-side form submission
- **Category filters**: Add dynamically loaded category filter chips showing only categories present in the user's favorites
- **Recipe cards**: Display recipe images (with fallback), heart/remove button, category badges, title, description, metadata (prep time, cook time, servings, difficulty), and real ratings
- **Empty state**: Polished empty state with "Explore Recipes" CTA linking to the real browse route
- **Navigation active state**: Add active state styling for the Favorites link in desktop and mobile navigation
- **Pagination**: Preserve search and category filters through pagination using `withQueryString()`

## Capabilities

### Modified Capabilities
- `favorites`: Adding three new requirements — server-side search, category filtering, and redesigned Blade UI with recipe cards, search bar, filter chips, and responsive grid (delta to existing spec)

### New Capabilities
_(none — all changes extend existing capabilities)_

## Impact

- **Controller**: `app/Http/Controllers/FavoriWebController.php` — enhanced `index()` method with search, category filtering, eager loading of avis, and `withQueryString()`
- **View**: `resources/views/favorites/index.blade.php` — complete rewrite from Bootstrap table to Tailwind recipe card grid
- **Layout**: `resources/views/layouts/dashboard.blade.php` — minor edit to add active navigation state for Favorites link
- **No route changes**: Existing `GET /favorites`, `POST /favorites`, `DELETE /favorites/{favori}` routes remain unchanged
- **No model changes**: Existing Favori, Recette, Category, Avis models are sufficient
- **No migration changes**: No database schema modifications needed
- **No new dependencies**: Uses existing Tailwind/Blade/Vite stack
