## 1. Image pipeline

- [x] 1.1 Create `app/Console/Commands/SyncRecipeImages.php` with a title → source file mapping for the 25 seeded recipes (skip `honey-glazed-salamon-with-roasted-carrots.png`) and a `--clear` rollback flag
- [x] 1.2 Command copies missing files from `Images/imagesRecipe/` to `public/images/recipes/<slug>.<ext>` using `Str::slug` on the title (e.g. `Crème Brûlée.webp` → `creme-brulee.webp`, `Soupe à l'Oignon.jpg` → `soupe-a-l-oignon.jpg`)
- [x] 1.3 Command updates `recettes.image_path` to `images/recipes/<slug>.<ext>` for every recipe with a matching source file (idempotent; `--clear` resets to NULL)
- [x] 1.4 Run `php artisan recipe:sync-images` and commit the resulting files under `public/images/recipes/`
- [x] 1.5 Verify all 25 seeded recipes have a non-null `image_path` pointing to an existing file, and that no recipe references the mockup image

## 2. Dashboard controller and route

- [x] 2.1 Create `app/Http/Controllers/DashboardController.php` (single `__invoke` action) preparing: authenticated user, featured recipe (`visibleTo` + `latest` + eager-loaded `categories` and `avis`), computed rating average/count, favorites count and up to 4 recent favorites, and time-based greeting
- [x] 2.2 Replace the `/dashboard` closure in `routes/web.php` with `Route::get('/dashboard', DashboardController::class)->name('dashboard')` (keep the `auth` middleware group)
- [x] 2.3 Ensure no dashboard query bypasses visibility rules (admins see all; hidden recipes only for their owner)

## 3. Dashboard layout and view

- [x] 3.1 Create `resources/views/layouts/dashboard.blade.php` (new file) following the `guest.blade.php` pattern: Inter + Playfair Display + Material Symbols Outlined font links and `@vite` for `app.css`/`app.js`; no Bootstrap
- [x] 3.2 Layout includes desktop top navigation: brand, Dashboard, Recipes, Generate with AI, My Favorites (all `route()` links), Create Recipe button → `recipes.create`, user initials avatar, and logout POST form with CSRF; no notification/settings icons, no `href="#"`
- [x] 3.3 Layout includes responsive mobile fixed bottom navigation: Dashboard, Recipes, AI Generator, Favorites (real routes only)
- [x] 3.4 Layout includes a footer with branding and only existing-route links
- [x] 3.5 Rewrite `resources/views/dashboard.blade.php` to `@extends('layouts.dashboard')`, rendering: hero with dynamic greeting + user name, "Generate with AI" → `generations.create`, "Create Manual Recipe" → `recipes.create`
- [x] 3.6 Featured recipe card: real image via `asset($recipe->image_path)` with title alt text (or placeholder if missing), title, description, category pill, prep/cook time, difficulty, and click through to `recipes.show`
- [x] 3.7 Rating block: show real average + review count when reviews exist; render "No reviews yet" (no stars, no numbers) when the recipe has no reviews
- [x] 3.8 AI Inspiration card: polished CTA to `generations.create` (no fake free-text submission)
- [x] 3.9 Favorites area: count + up to 4 recent favorites with image/title linking to each recipe; empty state "You haven't saved any favorites yet." linking to `/recipes`
- [x] 3.10 Empty featured state: "No recipes yet" card linking to `recipes.create` when no visible recipe exists
- [x] 3.11 Add minimal Tailwind utilities to `resources/css/app.css` only if required (e.g. line-clamp); reuse existing theme tokens; do not modify `tailwind.config.js`/`vite.config.js`

## 4. Responsive and accessibility pass

- [x] 4.1 Verify desktop layout: top nav, spacious container, multi-column hero/featured/AI/favorites arrangement
- [x] 4.2 Verify mobile layout: stacked content, full-width cards, fixed bottom nav visible, no horizontal overflow
- [x] 4.3 Accessibility: semantic HTML, visible focus states, sufficient contrast, descriptive alt text, keyboard-navigable links/buttons

## 5. Tests

- [x] 5.1 Create `tests/Feature/DashboardTest.php`: guest redirected to login; authenticated user gets 200
- [x] 5.2 Test that the dashboard renders the authenticated user's real name and initials
- [x] 5.3 Test that the dashboard renders the featured recipe's real title/description/category from the database
- [x] 5.4 Test that "No reviews yet" renders (and no rating stars/numbers) when the recipe has no reviews
- [x] 5.5 Test that a real review (via `Avis::factory()`) produces a rendered average/count
- [x] 5.6 Test that the featured image `src` points to `/images/recipes/` and the file exists via `public_path`
- [x] 5.7 Test favorites empty state, and that favorited recipes appear once a favorite exists
- [x] 5.8 Test that dashboard links (`/recipes`, `/generations/create`, `/favorites`, `/recipes/create`) are present
- [x] 5.9 Test visibility: a hidden recipe owned by another user is never the featured recipe for a non-admin
- [x] 5.10 Run the full test suite and confirm existing authentication/recipe/favorite/review/generation tests remain green

## 6. Verification

- [x] 6.1 Run `php artisan test` and `php artisan route:list` to confirm the dashboard route resolves
- [x] 6.2 Manual smoke test of `/dashboard` as the seeded user on desktop and mobile viewports (empty states visible: 0 reviews, 0 favorites)
- [x] 6.3 Confirm no protected files were modified (auth views/controllers/requests, recipes/favorites/reviews/generations/categories controllers and models, policies, migrations, API routes, Sanctum config, `vite.config.js`, `tailwind.config.js`, `layouts/app.blade.php`, admin views)
