## Context

See proposal.md — Why. Key constraints from the current codebase:

- `GET /dashboard` is a closure in `routes/web.php:30` returning a static Bootstrap view (`resources/views/dashboard.blade.php`). No controller, no data.
- The project already uses **Tailwind CSS v4** (CSS-first, no `tailwind.config.js`) via `@tailwindcss/vite` in `vite.config.js`. `resources/css/app.css` already defines the full Material "Organic Culinary Narrative" theme: all color tokens (`primary #376638`, `background #f9f9ff`, `tertiary #964327`, …), typography tokens (`font-headline-lg` = Playfair Display, `font-body-md` = Inter, …), and text sizes. `layouts/guest.blade.php` already loads Inter + Playfair Display + Material Symbols Outlined via Google Fonts and `@vite`.
- `resources/views/layouts/app.blade.php` still loads **Bootstrap 5 CDN** and is used by recipes/favorites/generations/admin views. It is on the protected list.
- Recipe images exist in `Images/imagesRecipe/` (25 real files, 1 mockup to exclude). `recettes.image_path` is NULL for all 25 rows. No upload code exists; `image_path` is a plain string column.
- Review aggregation pattern already exists: `RecipeWebController::show` computes `$recipe->avis->avg('rating')` + count; `AvisController::index` uses `withAvg`/`withCount`. Current DB: 0 reviews, 0 favorites.

## Goals / Non-Goals

**Goals:**
- Replace the static dashboard with a controller-backed page rendering only real data.
- Reuse the existing Tailwind v4 design system (colors/fonts/icons already in `app.css`/guest layout) — zero new frontend dependencies.
- Make all 25 recipe images web-accessible with slug-safe filenames and update `image_path` (data only, no migration).
- Provide honest empty states for reviews and favorites.
- Keep all protected files untouched.

**Non-Goals:**
- No recipe-view history / last-viewed tracking (no schema change).
- No avatar column, no notifications, no settings, no rating seeding.
- No changes to auth, recipes, favorites, reviews, generation, categories, API routes, policies, or migrations.
- No changes to the Bootstrap-based recipes/favorites/generations/admin views.

## Decisions

### D1: Dedicated `DashboardController` instead of closure
New `app/Http/Controllers/DashboardController.php` with an `__invoke` action; `routes/web.php` changes `Route::get('/dashboard', fn () => view('dashboard'))` → `Route::get('/dashboard', DashboardController::class)->name('dashboard')`. The controller prepares data; the view only renders.
- *Alternatives:* keep closure and query inline in Blade (rejected: violates "controller prepares data, view presents" and the project's thin-view convention); use a FormRequest (rejected: no input). Follows the existing single-action controller style used elsewhere (`AuthenticatedSessionController`).

### D2: Featured recipe = latest visible recipe
Featured recipe selected as `Recette::query()->visibleTo($request->user())->with(['categories', 'avis.user'])->latest()->first()`. "Latest" = most recently created (newest first). This is a real, meaningful ordering with no schema change.
- *Alternatives:* most favorited (rejected: 0 favorites → meaningless), highest rated (rejected: 0 reviews → meaningless), random (rejected: non-deterministic, untestable).
- Empty state: if no visible recipe, the featured section renders a "No recipes yet — create one" card linking to `/recipes/create`.

### D3: Ratings computed server-side from `avis`, never faked
The controller computes `$featured->avis` avg (rounded to 1 decimal) and count from the loaded relation (same pattern as `RecipeWebController::show`). View rules:
- count > 0 → show Material star icons for the real average + "(N reviews)" text.
- count = 0 → "No reviews yet" text; no stars, no numbers.
- Matches the spec scenarios exactly; future reviews automatically appear.

### D4: Images — idempotent `recipe:sync-images` artisan command + committed assets
New `app/Console/Commands/SyncRecipeImages.php` that:
1. Maps each seeded recipe title → source file in `Images/imagesRecipe/` → slug-safe destination `public/images/recipes/<slug>.<ext>` using `Str::slug($title)` and the source extension (e.g. `Crème Brûlée.webp` → `creme-brulee.webp`, `Coq au Vin.webp` → `coq-au-vin.webp`, `Soupe à l'Oignon.jpg` → `soupe-a-l-oignon.jpg`).
2. Copies files that are missing (idempotent, skips the `honey-glazed-salamon-with-roasted-carrots.png` mockup).
3. Updates `recettes.image_path` = `images/recipes/<slug>.<ext>` for every recipe with a matching source file.
- `--clear` flag to roll back: resets `image_path` to NULL (does not delete the copied files).
- The copied files are committed to `public/images/recipes/` so the app works without running the command (and tests can assert file existence).
- View renders `asset($recipe->image_path)` (stored without leading slash) with recipe title as `alt`; if `image_path` is null or the file is missing, render a styled placeholder div (no broken image).
- *Alternatives:* symlink (rejected: fragile on Windows/dev machines, deployment packaging), serving via a custom route (rejected: extra route + controller for static assets), storing raw filenames with spaces/accents (rejected: URL encoding bugs).
- Slug check (`Str::slug`): `Crème Brûlée`→`creme-brulee`, `Salade Niçoise`→`salade-nicoise`, `Soupe à l'Oignon`→`soupe-a-l-oignon`, `Boeuf Bourguignon`→`boeuf-bourguignon` — all URL-safe ASCII.

### D5: Dedicated `layouts/dashboard.blade.php` — do not touch `layouts/app.blade.php`
The dashboard gets its own layout (new file, mirrors the `guest.blade.php` pattern): Google Fonts links (Inter + Playfair Display + Material Symbols Outlined) + `@vite(['resources/css/app.css', 'resources/js/app.js'])`. It contains the desktop top nav (brand, Dashboard, Recipes, Generate with AI, My Favorites, Create Recipe, user initials avatar, logout form with CSRF), the mobile fixed bottom nav (Dashboard, Recipes, AI Generator, Favorites), and the footer.
- *Rationale:* `layouts/app.blade.php` is Bootstrap-based and protected; mixing Bootstrap CDN CSS with Tailwind on one page causes style conflicts. A dedicated layout avoids any risk to the other views. `dashboard.blade.php` switches from `@extends('layouts.app')` to `@extends('layouts.dashboard')`.
- Every link uses real routes (`route('dashboard')`, `route('recipes.index')`, `route('generations.create')`, `route('favorites.index')`, `route('recipes.create')`, logout POST). No `href="#"`. No notification/settings icons.

### D6: Hero greeting, avatar initials, AI card — all real-data or real-route
- Greeting: server-side `now()->format('H')`: <12 "Good Morning", <18 "Good Afternoon", else "Good Evening" + real user name.
- Avatar: initials from `$user->name` — take up to first two words, first letter each, uppercase (e.g. "Fatima Tester" → "FT"). No avatar DB field.
- AI Inspiration card: polished CTA linking to `generations.create` (the existing structured-ingredients flow). No free-text form that the backend cannot accept; card copy explains ingredients are entered in the generator.
- Favorites strip: count from `$user->favoris()->count()`, plus up to 4 recent favorited recipes (`$user->favoris()->with('recette.categories')->latest()->limit(4)`) with image + title linking to each recipe; if 0 → "You haven't saved any favorites yet." with a link to `/recipes`.

### D7: Tests — new `tests/Feature/DashboardTest.php`
Cover: guest redirect to login; authenticated 200; user name and initials rendered; featured recipe title from DB rendered; "No reviews yet" when avis empty (assert absence of star markup/ratings); rating shown after `Avis::factory()->create(...)`; image `src` contains `/images/recipes/` and file exists via `public_path`; favorites empty state; nav links present (`/recipes`, `/generations/create`, `/favorites`, `/recipes/create`); hidden-recipe visibility (featured never a hidden recipe of another user). Uses existing factories (`Recette::factory()`, `Avis::factory()`) already present in the test suite. Run the full suite to confirm existing auth/recipe tests remain green.

## Risks / Trade-offs

- **Bootstrap app layout untouched → visual inconsistency** between the new dashboard and the older Bootstrap pages → Mitigation: acceptable, deliberate; a future change can migrate other pages to the design system. No protected files are risked now.
- **"Featured = latest" ordering drifts** as recipes are added → Mitigation: acceptable behavior; the card is always a real, visible, published recipe. Deterministic and testable.
- **Image copy requires running the command** if `public/images/recipes/` is not committed → Mitigation: committed assets inside the change; command remains the source of truth for regenerating/sync.
- **Rendering rating from an eager-loaded collection** (not a single SQL aggregate) → Mitigation: only one featured recipe's reviews are loaded — negligible cost, matches the existing `RecipeWebController::show` pattern.
- **Fonts/Material icons loaded from Google CDN** (existing project pattern) → Mitigation: matches `guest.blade.php`; no new dependency, works offline with fallback fonts.
- **Str::slug mapping drift** if a recipe title changes → Mitigation: command maps by title and skips unmatched files; `image_path` only updated when a match exists.

## Migration Plan

1. Add `DashboardController`, `layouts/dashboard.blade.php`, rewrite `dashboard.blade.php`; change the `/dashboard` route.
2. Add `SyncRecipeImages` command; run `php artisan recipe:sync-images`.
3. Commit `public/images/recipes/*` assets.
4. Add `tests/Feature/DashboardTest.php`; run `php artisan test` (full suite).
5. Manual QA: `/dashboard` on desktop + mobile (stacked layout, bottom nav, no horizontal overflow); with seeded DB (0 reviews/favorites → empty states); keyboard navigation.
- **Rollback:** revert the route line and view files from git; run `php artisan recipe:sync-images --clear` to reset `image_path`; optionally delete `public/images/recipes/`. No migrations to reverse.

## Open Questions

None — the spec-level decisions (featured = latest, no last-viewed tracking, no avatar/notifications/settings, empty states, slug image mapping) are resolved above. Deferred niceties (e.g. free-text AI parsing, notification system, recipe-view history) are intentionally out of scope and would be separate changes.
