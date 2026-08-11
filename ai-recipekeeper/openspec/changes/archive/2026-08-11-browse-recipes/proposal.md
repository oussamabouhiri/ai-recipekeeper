## Why

The current recipe listing at `/recipes` is a management-oriented table showing only title, status, and date. There is no way for users to visually discover recipes, search by name or ingredients, filter by category, or see recipe cards with images, times, and difficulty at a glance. The dashboard shows a single featured recipe and a small favorites grid, but no comprehensive browse experience. This change adds a dedicated Browse Recipes page that serves as the primary recipe discovery surface, using the modern Tailwind/MD3 design system already established by the dashboard.

## What Changes

- Add a new `GET /browse` route with a dedicated controller method and Blade view
- Implement server-side search (title + description) and category filtering via GET parameters
- Display recipes in a responsive card grid (1/2/3/4 columns) with image, difficulty badge, favorite toggle, title, description, prep time, cook time, and servings
- Integrate the existing `Favori` system so authenticated users can favorite/unfavorite directly from browse cards
- Use the existing `layouts/dashboard.blade.php` for the modern Tailwind/MD3 design, mobile bottom nav, and desktop top nav
- Add the Browse page to navigation links in the dashboard layout
- Create feature tests covering search, category filtering, pagination, favorite state, and visibility

No new database tables, migrations, or recipe fields are introduced. No changes to existing CRUD behavior, API endpoints, or other views.

## Capabilities

### New Capabilities

- `browse-recipes`: A dedicated browse/discovery page where users can search recipes by title and description, filter by category, view recipe cards with metadata, and favorite/unfavorite recipes. Covers the new route, controller method, Blade view, search/filter query logic, card layout, and integration with existing favorites and visibility systems.

### Modified Capabilities

- `recipe-blade-ui` (navigation): The dashboard layout navigation links will be updated to include the Browse page. This is a minor navigation addition, not a change to recipe UI requirements.
- `dashboard` (navigation): The dashboard layout's desktop and mobile navigation will include a link to the Browse page. The dashboard content itself is unchanged.

## Impact

- **Files created**: `resources/views/recipes/browse.blade.php`, `tests/Feature/BrowseRecipesTest.php`
- **Files modified**: `app/Http/Controllers/RecipeWebController.php` (new `browse` method), `routes/web.php` (new route), `resources/views/layouts/dashboard.blade.php` (nav links)
- **API impact**: None. No new API endpoints. No changes to existing API behavior.
- **Database impact**: None. No migrations, no schema changes.
- **Dependency impact**: None. Uses existing Laravel, Tailwind, and Blade infrastructure.
- **Existing behavior**: No changes to `/recipes`, `/favorites`, `/dashboard`, or any API route. The existing `visibleTo` scope, `Favori` system, and `Category` relationship are reused as-is.
- **Test impact**: New test file. Existing tests remain unaffected.
