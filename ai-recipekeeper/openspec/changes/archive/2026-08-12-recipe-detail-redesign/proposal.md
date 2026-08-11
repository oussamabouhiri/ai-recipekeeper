## Why

The recipe detail page (`/recipes/{id}`) is the last major page still on the legacy Bootstrap 5 UI (`layouts/app.blade.php`), while the dashboard, browse, my recipes, create/edit recipe, favorites, and AI generator pages have already been redesigned onto the new Tailwind v4 + Material 3 design system (`layouts/dashboard.blade.php`). Every other recipe page links into this page, so it is the most visible inconsistency in the application and breaks the premium "AI Recipe Keeper" dashboard experience.

## What Changes

- **Rewrite** `resources/views/recipes/show.blade.php` to extend `layouts/dashboard.blade.php`, inheriting the existing desktop top navigation, mobile bottom navigation, and footer — no new layout is introduced.
- **Add a responsive hero image** section using the existing `image_path` mechanism with the established `file_exists` fallback placeholder convention.
- **Replace the Bootstrap card layout** with the design system's editorial layout: headline title, metadata bar (prep time, cook time, servings, difficulty, rating), action buttons (Favorite / Edit / Delete), a sticky ingredients card beside numbered preparation steps on desktop, and category chips.
- **Restyle the review section** (average rating, review list, create/edit/delete forms) using the existing design system components. All review behavior, routes, validation, and one-review-per-user rule are reused unchanged — no backend changes.
- **Keep all existing functionality intact**: dynamic recipe data for any `/recipes/{id}`, favorite toggle, edit/delete via existing policies, 404 for hidden recipes of other users, status badge (published/hidden), and author attribution.
- **Remove** the Bootstrap-only usage from this page; `layouts/app.blade.php` remains untouched (still used by the out-of-scope `generations/show` page).
- **No new dependencies, no new tables, no new routes, no API changes.**

## Capabilities

### New Capabilities

None — this is a visual migration of an existing page onto an existing design system.

### Modified Capabilities

- `recipe-blade-ui`: The "Recipe detail page" requirement changes to require the dashboard layout, hero image display, a metadata bar, action buttons, a responsive ingredients/steps split layout, and category chips; the "Authenticated app layout" requirement is updated to no longer mandate the legacy `layouts/app.blade.php` for recipe pages. Favorite-toggle and review-section requirements keep their behavior while their presentation is updated to the design system.

## Impact

- **Files modified**: `resources/views/recipes/show.blade.php` (rewritten); possibly minor additions to `resources/css/app.css` only if a new utility is needed (expected none).
- **Files reused unchanged**: `resources/views/layouts/dashboard.blade.php`, `app/Http/Controllers/RecipeWebController.php` (already supplies `$favorite`, `$userReview`, `$ratingAvg`, `$ratingCount`), `AvisWebController`, `FavoriWebController`, all policies and validation requests, `routes/web.php`.
- **Not affected**: API controllers/resources, database schema, `generations/show` page, admin pages, auth pages.
- **Risk surface**: preserving the one-review-per-user form/edit/delete flow, the CSRF-protected favorite toggle (store/destroy), the `file_exists` image fallback, and the non-Bootstrap delete confirmation (no Bootstrap JS is loaded by `layouts/dashboard`).