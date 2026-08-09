## Why

The backend Recipe CRUD is fully implemented (KAN-13), but there is no web interface to interact with it. Users cannot create, browse, view, edit, or delete recipes through the browser. This change adds the complete Blade frontend UI so users can manage recipes through a clean, responsive web interface, completing the user-facing recipe workflow.

## What Changes

- **New authenticated app layout**: A shared `layouts/app.blade.php` with responsive navbar, user info, and navigation links for authenticated users.
- **New dashboard route**: A `/dashboard` landing page for authenticated users showing their recipes.
- **Recipe list page**: Displays the authenticated user's recipes with title, status, and action buttons (view, edit, delete).
- **Recipe detail page**: Full display of a recipe including title, description, times, servings, difficulty, status, categories, ingredients with quantities, and ordered steps.
- **Create recipe form**: Blade form with fields for title, description, prep_time, cook_time, servings, difficulty, status, categories (multi-select), dynamic ingredient entries (ingredient_id + quantity + unit), and dynamic step entries (step_number + instruction).
- **Edit recipe form**: Pre-filled version of the create form, respecting owner/admin authorization.
- **Delete action**: Confirmation-based delete with CSRF protection, restricted to owner/admin.
- **New web routes and controller**: A `RecipeWebController` handling web routes under `/recipes` for list, show, create, store, edit, update, destroy.
- **Feature tests**: Blade UI feature tests covering list, show, create, edit, delete flows and authorization rules.

## Capabilities

### New Capabilities

- `recipe-blade-ui`: Blade-based web interface for recipe CRUD (list, show, create, edit, delete), including layout, routes, controller, and form handling.

### Modified Capabilities

- `recipe-management`: Add web routes and a web controller that reuse the existing API backend logic (validation, authorization, visibility). No changes to API behavior or data model.

## Impact

- **New files**: `resources/views/layouts/app.blade.php`, `resources/views/recipes/*.blade.php`, `app/Http/Controllers/RecipeWebController.php`, web routes.
- **Modified files**: `routes/web.php` (add recipe web routes).
- **No API changes**: The existing API routes and `RecetteController` remain untouched.
- **No database changes**: No migrations or schema changes.
- **No new dependencies**: Uses existing Bootstrap 5 CDN and Blade templating.
- **Tests**: New feature tests in `tests/Feature/RecipeBladeTest.php`.
