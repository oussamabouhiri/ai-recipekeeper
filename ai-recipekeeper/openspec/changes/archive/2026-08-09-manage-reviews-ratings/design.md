## Context

See proposal.md - Why for motivation. The app is a Laravel project (SQLite for tests, MySQL in production) with Sanctum bearer-token auth already in place. The reviews foundation already exists and requires no schema work:

- `avis` table (recette_id, user_id, rating, comment, timestamps) with `cascadeOnDelete` on both foreign keys and indexes on both columns. No unique constraint on `(user_id, recette_id)`.
- `Avis` model with `fillable = ['recette_id', 'user_id', 'rating', 'comment']`, `rating` cast to integer, `belongsTo` `recette` and `user`, and the `HasFactory` trait (no factory yet).
- `User::avis()` and `Recette::avis()` hasMany relationships already defined.
- `Recette::visibleTo()` scope, `User::isAdmin()`, owner-or-admin policies (`RecettePolicy`, `FavoriPolicy`), and the `admin` middleware all exist.
- Existing route patterns: API reads public, API writes under `auth:sanctum` (`routes/api.php`); web routes under `auth` (`routes/web.php`); favorites use `FavoriController` (API) + `FavoriWebController` (web) sharing a Form Request; Blade uses `layouts/app.blade.php` with Bootstrap 5 cards and server-rendered forms.
- `RecipeApiContractTest::test_show_response_exposes_exactly_the_mld_fields` pins the recipe API response to exactly the MLD fields — `RecetteResource` must not gain aggregate keys.

Constraints from the proposal: authenticated-only review writes; one review per user per recipe via validation only (no migration); users may review their own recipes; owner-or-admin update/delete; visibility gate so only viewable recipes can be reviewed; rating 1–5 with optional comment (max 1000); no AI; no recipe CRUD or favorites rewrite; no changes to the `RecetteResource` contract.

## Goals / Non-Goals

**Goals:**
- REST surface: `GET /api/recipes/{recipe}/reviews` (visibility-gated, public read), `POST /api/recipes/{recipe}/reviews` (authenticated), `PUT /api/reviews/{avis}` and `DELETE /api/reviews/{avis}` (owner or admin).
- Web surface: `POST /recipes/{recipe}/reviews`, `PUT /reviews/{avis}`, `DELETE /reviews/{avis}`.
- Reviews section on the recipe detail page: average rating, review count, review list, create form, and edit/delete actions.
- Rating aggregates (average rounded to 1 decimal, count) exposed by the reviews API response and rendered on the Blade detail page without touching `RecetteResource`.
- Policy-based authorization mirroring the `FavoriPolicy`/`RecettePolicy` pattern.
- One-review-per-user enforcement in the Form Request.
- `AvisResource` for API responses and `AvisFactory` for tests.
- Feature tests for CRUD, authorization, validation, aggregates, cascade, and Blade behavior.

**Non-Goals:**
- Schema changes of any kind (the `avis` table already satisfies the requirements; duplicate prevention is validation-only).
- Adding aggregates to `RecetteResource` or any other change to the recipe API contract.
- Review pagination customization, sorting/filtering beyond latest-first, or a dedicated standalone reviews page.
- Dashboard changes, admin-only review management pages, or review reply/flagging features.
- AI functionality and any other recipe CRUD or favorites behavior changes.

## Decisions

### Decision: Two controllers — API and web — following the favorites pattern
**Choice**: `AvisController` for API routes (JSON responses) and `AvisWebController` for web routes (Blade views + redirects), sharing `StoreAvisRequest`, `UpdateAvisRequest`, and `AvisPolicy`.
**Rationale**: Mirrors the established split used by recipes and favorites; keeps return types clean without `expectsJson()` branches.
**Alternatives**: One mixed controller with `expectsJson()` switching (rejected: diverges from the established two-controller pattern); API-only (rejected: project uses Blade per config).

### Decision: Owner-or-admin update/delete via `AvisPolicy`
**Choice**: `AvisPolicy` defines `update()` and `delete()`, both returning `$user->isAdmin() || $user->id === $avis->user_id`. Creation is guarded by `auth:sanctum`/`auth` middleware and validation, matching favorites.
**Rationale**: Reviews are user-owned resources; owner-or-admin is the convention across `FavoriPolicy` and `RecettePolicy`, and the authorization spec's policy foundation covers admin moderation of user-owned resources.
**Alternatives**: Owner-only write access (rejected: inconsistent with the admin-moderation pattern); inline checks (rejected: diverges from the policy foundation).

### Decision: One review per user per recipe via Form Request after-hook, no migration
**Choice**: `StoreAvisRequest` validates `rating` (required, integer, between 1 and 5) and `comment` (nullable, string, max 1000), and rejects duplicates with a validator after-hook: if the authenticated user already has an `Avis` for the route-bound recipe, the request fails with a 422 validation error. No database constraint is added — the `avis` table stays untouched per the no-schema-change constraint.
**Rationale**: The `recette_id` comes from the route, not the client payload, so a field-level `unique` rule on client input (the favorites approach) cannot express the check; an after-hook on the validated recipe id is the idiomatic equivalent and keeps the 422 contract.
**Alternatives**: `firstOrCreate` returning the existing review (rejected: hides user errors, not in spec); adding a `unique(user_id, recette_id)` migration (rejected: violates the explicit no-database-change constraint); controller-level `ValidationException` (rejected: moves validation out of the Form Request).

### Decision: `UpdateAvisRequest` does not extend `StoreAvisRequest`
**Choice**: `UpdateAvisRequest` is a standalone Form Request with the same `rating`/`comment` rules but no uniqueness after-hook.
**Rationale**: `UpdateRecetteRequest` extends its store sibling, but here the uniqueness check is store-only — extending would wrongly reject a user updating their own review.
**Alternatives**: Conditional after-hook inside `StoreAvisRequest` (rejected: couples the request to the route); reusing the store request (rejected: breaks the update flow).

### Decision: Visibility gate before creating or listing reviews
**Choice**: `index` and `store` resolve the recipe with `Recette::query()->visibleTo($request->user())->findOrFail($recipe->id)`; a hidden recipe the user cannot see responds 404. Update/delete operate on the `Avis` directly and rely on the Policy.
**Rationale**: A user should never see or review a recipe they are not allowed to view; `visibleTo` already encodes the exact visibility rules.
**Alternatives**: Accept any existing recipe id (rejected: leaks hidden recipes); duplicating visibility logic (rejected: DRY violation).

### Decision: Aggregates on the reviews response, not on `RecetteResource`
**Choice**: The API list endpoint computes the recipe's `rating_avg` and `rating_count` with `withAvg`/`withCount` and merges them into the response via `AvisResource::collection(...)->additional(['rating_avg' => ..., 'rating_count' => ...])`. The Blade detail page computes the same values from the eager-loaded relationship. `RecetteResource` is untouched.
**Rationale**: `RecipeApiContractTest` asserts the recipe response exposes exactly the MLD fields; adding aggregates there would break the existing contract test and contradict the "do not modify the existing database design" constraint. Aggregates are review data, so the reviews endpoint is their natural home.
**Alternatives**: Adding `rating_avg`/`rating_count` to `RecetteResource` (rejected: breaks the pinned API contract); a separate aggregate endpoint (rejected: unnecessary round trip); storing computed columns (rejected: schema change).

### Decision: `AvisResource` with nested author
**Choice**: `AvisResource` whitelists `id, recette_id, user_id, rating, comment, created_at, updated_at` and nests the author as `{id, name}` via `whenLoaded('user')`.
**Rationale**: Follows the explicit-whitelist pattern of `RecetteResource`/`FavoriResource`; the review list needs author names for display.
**Alternatives**: Returning models directly (rejected: no shape control); nesting the full user (rejected: over-exposes user data — the recipe resource already trims users to `{id, name}`).

### Decision: Cascade deletes require no code
**Choice**: Recipe/user deletion cleans up reviews via the existing `cascadeOnDelete` foreign keys; this change only adds tests asserting the behavior.
**Rationale**: The migration already defines the cascade; manual deletion would duplicate the database's job.
**Alternatives**: Manual deletion in `RecetteController::destroy` (rejected: redundant, risks breaking existing CRUD).

### Decision: Embedded reviews section on the recipe detail page
**Choice**: `RecipeWebController::show()` eager-loads the recipe's reviews with authors, computes the current user's review plus the average and count, and passes them to the view. `recipes/show.blade.php` renders a reviews card under the existing recipe content: summary line, review list, and — server-rendered, no JavaScript — a create form (when the user has no review), an edit form and delete button (for the user's own review), and a delete button on every review for admins.
**Rationale**: Mirrors the favorites toggle approach (state decided server-side, plain forms with CSRF); no new page or layout needed; the app layout already provides success alerts and validation rendering.
**Alternatives**: A standalone `/reviews` page (rejected: reviews belong to a recipe; the detail page is the natural context); JS-driven AJAX forms (rejected: project uses vanilla Blade/forms); a separate reviews controller route rendering its own page (rejected: unnecessary page).

### Decision: `AvisFactory` with explicit defaults
**Choice**: `AvisFactory` defaulting `user_id` and `recette_id` to factories, `rating` to a random 1–5, and `comment` to a sentence.
**Rationale**: Tests need deterministic reviews; both relationships are required columns and `rating` is NOT NULL.
**Alternatives**: No factory (rejected: tests would inline `Avis::create` calls everywhere).

## Risks / Trade-offs

- **No database unique constraint** → The after-hook validation rejects the common duplicate case, but two concurrent requests can both pass validation and insert duplicate rows (favorites has a DB backstop; reviews cannot, per the no-schema-change constraint). Accepted trade-off; double-submits from a single user are the realistic window.
- **Modifying `recipes/show.blade.php` and `RecipeWebController`** → The change is additive (one eager-load plus a reviews card); existing `RecipeBladeTest` assertions and recipe API contract tests keep passing.
- **Rating average precision** → `AVG` over integer ratings yields a float (e.g., 4.666…); rounded to 1 decimal for display, null when there are no reviews. Rounding is a task-level knob, not a spec dependency.
- **Large review lists on the detail page** → The Blade section lists all reviews (typically few); the API list endpoint paginates like favorites.
- **Validation error key for duplicates** → The after-hook attaches the error to `rating`; the web form shows it inline and the API returns 422 with the errors payload. No spec depends on the exact key.

## Migration Plan

1. No new migrations — the `avis` table already exists with cascade deletes and indexes.
2. Implementation order: `AvisPolicy` → `StoreAvisRequest`/`UpdateAvisRequest` → `AvisResource` → `AvisFactory` → `AvisController` + `AvisWebController` → routes (web + API) → reviews section on `recipes/show.blade.php` + `RecipeWebController::show()` → tests.
3. Feature branch per GitFlow (e.g. `feature/manage-reviews-ratings`); deploy is standard Laravel (no migrations); rollback is a code-only revert of the controllers, routes, views, requests, policy, resource, and factory.
4. Verification: `php artisan test`, `vendor/bin/pint`, `openspec validate`.

## Open Questions

- **Average rating precision**: 1 decimal chosen; can be adjusted at implementation time without spec or task changes.
- **Reviews list pagination size**: Laravel default 15 for the API endpoint; the Blade section is unpaginated. Neither the specs nor the approach depend on a page size.
