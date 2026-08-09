## Context

See proposal.md - Why for motivation. The app is a Laravel 12/13 project (SQLite for tests, MySQL in production) with Sanctum bearer-token auth already in place. The favorites foundation already exists and requires no schema work:

- `favoris` table (user_id, recette_id, timestamps) with a `unique(user_id, recette_id)` constraint, `cascadeOnDelete` on both foreign keys, and indexes on both columns.
- `Favori` model with `fillable = ['user_id', 'recette_id']`, `belongsTo` `user` and `recette`, and the `HasFactory` trait (no factory yet).
- `User::favoris()` and `Recette::favoris()` hasMany relationships already defined.
- `Recette::visibleTo()` scope, `User::isAdmin()`, `RecettePolicy`/`UserPolicy` (owner-or-admin pattern), and the `admin` middleware all exist.
- Existing route patterns: API writes under `auth:sanctum` (`routes/api.php`), web pages under `auth` (`routes/web.php`); recipes use two controllers (`RecetteController` API + `RecipeWebController` web) sharing Form Requests; Blade uses `layouts/app.blade.php` with Bootstrap 5 cards and a dark navbar.

Constraints from the proposal: authenticated-only favorites; owner-or-admin removal; duplicate prevention via the existing unique constraint; visibility gate so only viewable recipes can be favorited; no schema changes; no AI; no recipe CRUD rewrite.

## Goals / Non-Goals

**Goals:**
- REST surface: `GET /api/favorites`, `POST /api/favorites`, `DELETE /api/favorites/{favori}` — all authenticated, removal owner-or-admin.
- Web surface: `GET /favorites` (My Favorites page), `POST /favorites`, `DELETE /favorites/{favori}`.
- Favorite toggle on the recipe detail page driven by the current favorite state.
- "My Favorites" link in the shared authenticated navbar.
- Policy-based authorization for removal mirroring the `UserPolicy`/`RecettePolicy` pattern.
- Validation via a Form Request with per-user duplicate prevention.
- `FavoriResource` for API responses and `FavoriFactory` for tests.
- Feature tests for CRUD, authorization, validation, cascade, and Blade behavior.

**Non-Goals:**
- Schema changes of any kind (migration already satisfies the requirements).
- Favorite counts/aggregations, sorting/filtering beyond latest-first listing.
- Sharing favorites or making them visible to other users.
- AI functionality and any other recipe CRUD behavior changes beyond the toggle.

## Decisions

### Decision: Two controllers — API and web — following the recipe pattern
**Choice**: `FavoriController` for API routes (JSON responses) and `FavoriWebController` for web routes (Blade views + redirects), both sharing `StoreFavoriteRequest` and `FavoriPolicy`.
**Rationale**: This mirrors the existing recipe split (`RecetteController` + `RecipeWebController`) and keeps return types clean — API methods return `FavoriResource`/JSON, web methods return views/`RedirectResponse`. A single mixed controller would need `request()->expectsJson()` branches.
**Alternatives**: One `FavoriController` with `expectsJson()` switching (rejected: diverges from the established two-controller pattern); API-only with a JavaScript frontend (rejected: project uses Blade per config).

### Decision: Owner-or-admin removal via `FavoriPolicy`
**Choice**: `FavoriPolicy::delete()` returns `$user->isAdmin() || $user->id === $favori->user_id`; no `create`/`update` methods — creation is guarded by the `auth:sanctum`/`auth` middleware and validation, matching how recipe creation works today.
**Rationale**: Favorites are user-owned resources; the owner-or-admin rule is the established convention across `UserPolicy`, `RecettePolicy`, and the authorization spec's policy foundation.
**Alternatives**: Owner-only deletion (rejected: inconsistent with the admin-moderation pattern used by every other policy in the app); no policy with inline checks (rejected: diverges from the established authorization foundation).

### Decision: Duplicate prevention via scoped unique validation plus DB constraint
**Choice**: `StoreFavoriteRequest` validates `recette_id` with `unique:favoris,recette_id,NULL,id,user_id,{auth id}` so a duplicate returns 422; the existing `unique(user_id, recette_id)` constraint remains the hard backstop.
**Rationale**: Validation gives a friendly 422 for the common case and the database constraint guarantees integrity under race conditions — no migration needed since the constraint already exists.
**Alternatives**: Idempotent `firstOrCreate` returning the existing favorite (rejected: hides user errors, no spec support for it); raw DB exception handling (rejected: fragile, no user-facing error).

### Decision: Visibility gate before creating a favorite
**Choice**: In the store path, resolve the recipe with `Recette::query()->visibleTo($request->user())->findOrFail($recetteId)`; a hidden recipe the user cannot see responds 404, and a non-existent recipe is caught by the `exists:recettes,id` validation rule (422).
**Rationale**: A user should never bookmark a recipe they are not allowed to see — the existing `visibleTo` scope already encodes the exact visibility rules and requires no new logic.
**Alternatives**: Accept any existing recipe id (rejected: lets users bookmark recipes they cannot view); duplicating visibility logic (rejected: DRY violation).

### Decision: `FavoriResource` with nested recipe
**Choice**: `FavoriResource` whitelists `id, user_id, recette_id, created_at, updated_at` and nests the recipe via `RecetteResource::make($this->whenLoaded('recette'))`.
**Rationale**: Follows the `RecetteResource`/`CategoryResource` explicit-whitelist pattern; the list page (API and Blade) needs full recipe data to render favorite cards.
**Alternatives**: Return models directly (rejected: no shape control); flat recipe fields on the favorite (rejected: duplicates `RecetteResource` logic).

### Decision: Cascade deletes require no code
**Choice**: Recipe/user deletion cleans up favorites via the existing `cascadeOnDelete` foreign keys; this change only adds tests asserting the behavior.
**Rationale**: The migration already defines the cascade; adding manual `delete()` calls would duplicate the database's job.
**Alternatives**: Manual deletion in `RecetteController::destroy` (rejected: redundant, risks breaking existing CRUD).

### Decision: Eager-loaded My Favorites page with latest-first ordering
**Choice**: The web index queries `$user->favoris()->with('recette.user', 'recette.categories')->latest()` and renders `favorites/index.blade.php` extending `layouts/app.blade.php`, with recipe cards, view links, remove buttons, and an empty state.
**Rationale**: Avoids N+1 queries, matches the existing recipe list card/table styling, and the app layout already provides the navbar, success alerts, and logout.
**Alternatives**: Reuse the recipe index page with a filter (rejected: mixes concerns and alters existing recipe UI); paginate differently (rejected: default pagination matches existing pages).

### Decision: Favorite toggle on the recipe detail page
**Choice**: `RecipeWebController::show()` additionally loads the current user's favorite for the recipe and passes it to the view; `recipes/show.blade.php` renders a POST form to add or a DELETE form to remove accordingly. The navbar gains a "My Favorites" link.
**Rationale**: Minimal, non-breaking touch to recipe UI — the only change is one query and one form, and the favorite state is rendered server-side so no JavaScript is needed.
**Alternatives**: JS-driven AJAX toggle (rejected: project uses vanilla Blade/forms with CSRF); a link out to the favorites page only (rejected: toggle on the recipe is the expected UX).

### Decision: `FavoriFactory` with explicit defaults
**Choice**: `FavoriFactory` defaulting `user_id` and `recette_id` to factories, matching the `RecetteFactory` style.
**Rationale**: Tests need to create favorites deterministically; both relationships are required columns.
**Alternatives**: No factory (rejected: tests would inline `Favori::create` calls everywhere).

## Risks / Trade-offs

- **Race condition on duplicate insert** → Mitigation: the `unique(user_id, recette_id)` constraint rejects the second row even if two requests pass validation simultaneously.
- **Modifying `recipes/show.blade.php` and `RecipeWebController`** → Mitigation: the change is additive (one eager-load and one form); existing recipe tests keep passing and no recipe logic is altered.
- **Favorite list grows large** → Mitigation: standard pagination on API and web, matching existing pages; the spec does not depend on a page size.
- **Removing a favorite you do not own leaks existence info** → Mitigation: 403 is returned for non-owner removal, consistent with existing policy behavior; a hidden recipe is still unreachable via 404.
- **Nested `recette` in `FavoriResource` leaks hidden recipes** → Mitigation: favorites only exist for recipes the user could view at the time of favoriting; if a recipe becomes hidden later, its favoriting user still owns the favorite, which is the intended behavior.

## Migration Plan

1. No new migrations — the `favoris` table already exists with the unique constraint and cascade deletes.
2. Implementation order: `FavoriPolicy` → `StoreFavoriteRequest` → `FavoriResource` → `FavoriFactory` → `FavoriController` + `FavoriWebController` → routes (web + API) → Blade views (favorites index, navbar link, recipe show toggle) → tests.
3. Feature branch per GitFlow (e.g. `feature/manage-favorites`); deploy is standard Laravel (no migrations); rollback is a code-only revert of the controllers, routes, views, and policy.
4. Verification: `php artisan test`, `vendor/bin/pint`, `openspec validate`.

## Open Questions

- **Pagination size** for `GET /api/favorites` and `/favorites`: Laravel default 15 for now; a task-level knob neither the specs nor the approach depend on.
- **Favorites count card on the dashboard**: not in scope; can be added later without spec changes.
