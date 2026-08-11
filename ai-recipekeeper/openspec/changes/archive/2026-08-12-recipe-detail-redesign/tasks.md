## 1. Layout Switch

- [x] 1.1 Change `resources/views/recipes/show.blade.php` to `@extends('layouts.dashboard')` and keep `@section('title', $recipe->title)`
- [x] 1.2 Add the `$user`/`$initials` computation to `RecipeWebController@show` (required by `layouts.dashboard` avatar cell; same pattern as every other dashboard action)

## 2. Hero and Header

- [x] 2.1 Add full-width responsive hero image section (`h-64 md:h-96`, `object-cover`, `md:rounded-xl`) using `asset($recipe->image_path)` with the `file_exists` fallback placeholder (restaurant icon) already used across dashboard pages
- [x] 2.2 Render the centered headline with the recipe title (`font-display-lg`, responsive sizing), author `By {{ $recipe->user->name }}` and `created_at` date line, and the published/hidden status badge

## 3. Metadata Bar and Action Row

- [x] 3.1 Build the centered metadata bar with Material Symbols icon + value pairs for prep time, cook time, servings, and difficulty, each rendered only when non-null, with `hidden md:block` dividers
- [x] 3.2 Add the rating summary (average rating + review count, or "No reviews yet") into the metadata bar using the star icon and fill-variation pattern from the dashboard featured card
- [x] 3.3 Render the action row: Favorite toggle switching between `favorites.store` and `favorites.destroy` forms based on `$favorite`; Edit guarded by `@can('update', $recipe)`; Delete guarded by `@can('delete', $recipe)`
- [x] 3.4 Replace the Bootstrap delete modal with a plain DELETE form using a native `confirm()` dialog (no Bootstrap JS available in `layouts.dashboard`)

## 4. Main Content Split

- [x] 4.1 Build the two-column desktop grid (`grid-cols-1 md:grid-cols-12`): ingredients card in `md:col-span-4` with `md:sticky top-24` and quantity/unit display, steps in `md:col-span-8` with `bg-primary` numbered circles and `step_number` ordering
- [x] 4.2 Stack single-column layout on mobile with no horizontal scrolling

## 5. Categories and Description

- [x] 5.1 Render description block when present
- [x] 5.2 Render category chips (dashboard `bg-tertiary/10` chip pattern) only when the recipe has categories

## 6. Reviews Section Restyle

- [x] 6.1 Restyle the rating summary header (average, count, review list) with the design system's components
- [x] 6.2 Restyle the create-review form (rating select 1–5, optional comment) using dashboard input conventions, still posting to `reviews.store` and keeping the one-review-per-user validation error display
- [x] 6.3 Restyle the user's own review card with edit and delete actions (edit form toggled by a small vanilla-JS listener replacing `data-bs-toggle="collapse"`), posting to `reviews.update` / `reviews.destroy`
- [x] 6.4 Render the remaining reviews list with admin delete actions per `@can('delete', $review)`, excluding the user's own review as the legacy template does

## 7. Verification

- [x] 7.1 Render `/recipes/{id}` for multiple recipe ids and assert real data shows: hero, metadata, ingredients with pivot quantity/unit, ordered steps, chips, reviews
- [x] 7.2 Verify edge states: recipe without image (fallback placeholder), recipe without reviews, hidden recipe of another user (404), own hidden recipe (visible with hidden badge)
- [x] 7.3 Verify authorization states: owner sees Favorite/Edit/Delete, non-owner sees only Favorite, admin sees Edit/Delete on any visible recipe, admin sees delete on all reviews
- [x] 7.4 Exercise review flows: create review, duplicate review rejected with error, edit own review, delete own review
- [x] 7.5 Verify responsive behavior at desktop (>= 1024px) and mobile (< 1024px) widths: split vs stacked layout, sticky card, bottom navigation, no horizontal scroll
- [x] 7.6 Confirm no other pages regress: run existing test suite (`php artisan test` or configured runner) if available