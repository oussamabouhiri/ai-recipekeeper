## Why

The current `/dashboard` is a static Bootstrap page showing only the authenticated user's recipe count, while the product now has 25 real seeded recipes, full model relationships (categories, ingredients, etapes, avis, favoris), recipe images, and an AI generation flow. The dashboard is the first screen users see and does not reflect the application's actual capabilities.

## What Changes

- Replace the static dashboard closure (`routes/web.php`) with a dedicated dashboard controller/action that prepares real dashboard data using existing models, relationships, and the `visibleTo()` scope.
- Rebuild `resources/views/dashboard.blade.php` into a polished, responsive, culinary dashboard inspired by the reference design (visual language only), using the project's Tailwind setup; no Bootstrap CDN, no fake data, no external images.
- Add a dynamic time-based greeting ("Good Morning / Good Afternoon / Good Evening") using the authenticated user's real name.
- Add a Featured Recipe card backed by a real published recipe (prefer the latest recipe visible to the user), linking to `GET /recipes/{recipe}`. It shows real image, title, description, category, prep/cook time, difficulty, and real review information — with an explicit "No reviews yet" empty state since `avis` is currently empty (0 reviews).
- Add an AI Inspiration card that is a polished call-to-action to the existing `/generations/create` flow (the backend expects structured ingredients; no fake free-text AI submission).
- Add a user avatar built from the authenticated user's real initials (no avatar field exists; none will be added).
- Add desktop navigation, mobile bottom navigation, and a simple footer using only existing routes. No notifications/settings icons, no fake Privacy/Terms/Help links.
- Make all 25 real recipe images web-accessible: copy files from `Images/imagesRecipe/` to `public/images/recipes/` with URL-safe slug filenames, and update each recipe's existing `image_path` column accordingly (data update only; no schema change). The extra mockup image `honey-glazed-salamon-with-roasted-carrots.png` must NOT be used (no matching recipe).
- Add/update feature tests covering dashboard access, real data rendering, empty states, image paths, route validity, and that existing auth/recipe functionality remains unaffected.

## Capabilities

### New Capabilities

- `dashboard`: Dashboard page behavior — authenticated access, real user/recipe data presentation, featured recipe selection, empty states for reviews/favorites, navigation, and image display.

### Modified Capabilities

None. No existing capability's externally observable behavior changes: recipe pages, favorites, reviews, and generation flows are untouched, and the `image_path` data update is internal data (currently no page renders recipe images).

## Impact

- **Routes/controllers**: `GET /dashboard` moves from closure to `DashboardController` (web.php only; `routes/api.php` untouched).
- **Views**: `resources/views/dashboard.blade.php` rewritten; layouts untouched (dashboard uses existing `layouts.app` unless strictly required, and if so the change is explained and justified).
- **Data**: 25 `UPDATE recettes SET image_path = ...` rows; no schema/migration changes.
- **Files/assets**: 25 image files copied `Images/imagesRecipe/` → `public/images/recipes/` with slug-safe names.
- **Tests**: new `tests/Feature/DashboardTest.php` (or similar).
- **Protected**: auth controllers/requests/views, recipe/favorite/review/generation/category controllers and models, policies, API routes, Sanctum config, `vite.config.js`, `tailwind.config.js` — not modified. `tailwind.config.js` and `vite.config.js` are only touched if strictly required and explicitly justified.
- **Dependencies**: none new; fonts/icons only reused if already present in the project.
