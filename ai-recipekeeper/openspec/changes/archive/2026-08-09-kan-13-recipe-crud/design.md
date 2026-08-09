## Context

See proposal.md - Why for motivation. The app is a Laravel 12/13 project (SQLite for tests, MySQL in production) with Sanctum bearer-token auth already in place (`routes/api.php` has an `auth:sanctum` group with `/user` and `/tokens`). The database schema and Eloquent models already exist:

- `recettes` table (title, description, `instructions`, prep_time, cook_time, servings, difficulty, image_path, user_id, is_ai_generated, timestamps) — no `statut` column yet.
- `etapes` (recette_id, step_number, instruction) with `Recette::etapes()` hasMany already defined.
- `recette_ingredient` pivot (recipe_id, ingredient_id, quantity, unit) and `recette_categorie` pivot; `Recette::ingredients()` does NOT yet load pivot `quantity`/`unit`.
- `User::isAdmin()`, `UserPolicy` (owner-or-admin pattern), and admin seeder already exist.
- No recipe controller, policy, Form Request, resource, factory, or seeder exists.

Constraints from the approved proposal: the MLD is the source of truth for the database structure; `recettes.instructions` is a documented implementation/schema discrepancy that is NOT part of the KAN-13 API contract and is not created/removed/renamed; `etapes` is the authoritative structured step relationship; `image_path` is plain data (no upload/storage); users choose `published`/`hidden` for their own recipes (no admin-only publishing); no model renames; scope is Recipe CRUD, visibility, authorization, relationships, validation, and tests.

## Goals / Non-Goals

**Goals:**
- One additive, reversible migration adding `recettes.statut` (enum `published`/`hidden`, default `published`) — works on MySQL and SQLite (Laravel's SQLite grammar compiles enum to `varchar check`).
- REST surface: `GET/POST /api/recipes`, `GET/PUT/DELETE /api/recipes/{recipe}`; read endpoints public, write endpoints under `auth:sanctum`.
- Visibility semantics in a single query path: published to everyone, hidden to owner and admins; non-visible single-recipe access returns 404.
- Transactional persistence of recipe + `Etape` records + ingredient/category pivots on create and update.
- An explicit API response shape that excludes `recettes.instructions` by construction.
- Policy-based authorization for update/delete (owner-or-admin), mirroring `UserPolicy`.
- Feature tests for CRUD, visibility, authorization, and validation.

**Non-Goals:**
- Image upload/storage (any handling of `image_path` stays as plain data; no storage driver, no multipart payloads).
- Admin-only publishing/moderation workflow; `is_ai_generated` is stored (per MLD) but not client-settable in KAN-13.
- New tables/entities, model renames, changes to `recettes.instructions` (create/remove/rename), AI generation, reviews, favorites, dedicated management of ingredients/categories/etapes.
- Authorization for the index/list endpoint (visibility query, not Policy, governs listing).

## Decisions

### Decision: Native `enum` column for `statut`, plain string on the model
**Choice**: `$table->enum('statut', ['published', 'hidden'])->default('published')` in a new reversible migration; `statut` added to `Recette::$fillable` and validated via `in:published,hidden`; no PHP enum/cast introduced.
**Rationale**: Matches the MLD exactly (`ENUM('published','hidden')`). Laravel's SQLite grammar compiles enum to `varchar check (...)`, so the same migration works in tests (SQLite) and production (MySQL). A string + validation rule keeps the change minimal; a PHP enum cast can be layered on later without a schema change.
**Alternatives**: `string` column + check constraint (rejected: diverges from the MLD); PHP `BackedEnum` cast (deferred — no contract benefit yet, adds surface for no requirement).

### Decision: API Resource (`RecetteResource`) whitelisting fields, excluding `instructions`
**Choice**: A `RecetteResource` that explicitly selects `id, title, description, prep_time, cook_time, servings, difficulty, image_path, statut, is_ai_generated, user_id, created_at, updated_at` plus nested `user`, `etapes` (ordered by `step_number`), `ingredients` (with pivot `quantity`/`unit`), and `categories`. Store/update payloads also exclude `instructions` and `user_id`.
**Rationale**: The `instructions` column exists on the model, so returning the model as-is would leak it into the API contract. A whitelist makes the contract explicit and testable (tests assert `instructions` never appears), and gives a stable nested shape for steps/ingredients/categories.
**Alternatives**: `$hidden` on the model (rejected: hides the column app-wide, obscures the discrepancy); manual `->only()`/`array_map` in the controller (rejected: verbose, harder to test, duplicated shape).

### Decision: Visibility as a query scope, not a Policy
**Choice**: `Recette::scopeVisibleTo(?User $user)` applying `statut = 'published'` OR (`statut = 'hidden'` AND `user_id = $user?->id`) OR (`$user?->isAdmin()` → no filter). Used by both `index` (list) and `show` (single recipe; hidden non-owner falls through to 404).
**Rationale**: Listing is data-scoping, not capability-based; a query scope keeps one visibility definition in one place and matches the spec's 404 semantics ("responds as if the recipe does not exist") which a Policy 403 would contradict.
**Alternatives**: Policy `viewAny`/`view` for listing (rejected: policies can't scope collections without custom gate logic and would return 403, not 404); inline `where` clauses in the controller (rejected: duplicated across index/show, untestable in isolation).

### Decision: Policy gates only update/delete
**Choice**: `RecettePolicy::update`/`delete` return `$user->isAdmin() || $user->id === $recette->user_id`, mirroring `UserPolicy`; policy methods are auto-discovered (convention, `App\Models\Recette` → `App\Policies\RecettePolicy`).
**Rationale**: The spec demands 403 for non-owner update/delete, which is exactly `$this->authorize()`. The app already relies on policy auto-discovery (`UserPolicy` works without an explicit `AuthServiceProvider`).
**Alternatives**: Inline `abort_unless` in the controller (rejected: diverges from the established `UserPolicy` pattern the proposal mandates).

### Decision: Full-replacement semantics on update
**Choice**: Inside a `DB::transaction`, update the recipe row, then `$recipe->etapes()->delete()` + `createMany()` with the payload steps, and `sync()` categories / `syncWithPivotDetached()` ingredients (with `quantity`/`unit`). Payload shapes: `etapes: [{step_number, instruction}]`, `ingredients: [{ingredient_id, quantity?, unit?}]`, `categories: [id, ...]`.
**Rationale**: `etapes` are ordered child rows with no natural identity; replacing them is simpler and safer than diffing. Pivot syncs are the standard Laravel idiom for many-to-many with extra columns.
**Alternatives**: Diffing/upserting steps (rejected: complex, no benefit for a JSON API contract); `createOrUpdate` per pivot (rejected: `sync` already handles detach + extras).

### Decision: Validation lives in Form Requests
**Choice**: `StoreRecetteRequest`/`UpdateRecetteRequest` with `rules()` covering title (required), statut (`in:published,hidden`), numeric non-negative prep_time/cook_time, servings `min:1`, etapes array (each: `step_number` `integer|min:1`, `instruction` `required`), ingredients array (each: `ingredient_id` `exists:ingredients,id`, quantity/unit nullable strings), categories array of `exists:categories,id`. `authorize()` returns `true` (per-action authorization happens in the controller via Policy); `user_id` and `is_ai_generated` are never accepted.
**Rationale**: Form Requests centralize validation, keep the controller lean, and make 422 behavior trivially testable.
**Alternatives**: Inline `$request->validate()` (rejected: bloats controller, duplicated across store/update).

### Decision: Listing paginated; eager loading throughout
**Choice**: `index` returns `RecetteResource::collection(Recette::visibleTo($user)->with(['user','etapes','ingredients','categories'])->paginate())`; `show` loads the same relations. List ordering by `created_at` desc.
**Rationale**: Pagination is the standard Laravel API default (frontend gets paginator meta); eager loading avoids N+1. Fine-grained count/order tuning is deferrable.
**Alternatives**: Unpaginated collection (rejected: no growth path); cursor pagination (rejected: over-engineering for this stage).

### Decision: Factory produces the recipe row; seeder wires related data
**Choice**: `RecetteFactory` with default `statut = 'published'` and a `hidden()` state; no `afterCreating` wiring. `RecetteSeeder` creates recipes and attaches etapes/ingredients/categories explicitly, and registers in `DatabaseSeeder`. Tests create related models (Etape, Ingredient, Category) explicitly per scenario.
**Rationale**: Keeps the factory deterministic and the seeder readable; tests stay explicit about what they set up.
**Alternatives**: Factory `afterCreating` with random children (rejected: nondeterministic tests, hides setup).

## Risks / Trade-offs

- **`instructions` leaking into responses** → Mitigation: `RecetteResource` whitelist + feature test asserting `instructions` is absent from store/show responses and payloads; the discrepancy stays documented in proposal.md and the database-schema delta spec.
- **Enum portability (MySQL vs SQLite)** → Mitigation: verified Laravel's SQLite grammar compiles enum to `varchar check`; the migration runs identically in both, and the test suite (SQLite) exercises it.
- **`statut` defaulting to `published`** silently publishes all existing rows when the migration runs → Mitigation: accepted — MLD default is `published`; existing seeded/dev rows becoming published is the intended visibility model, and KAN-13's tests assert the default.
- **Full-replacement etapes loses step ids on update** → Mitigation: accepted for the JSON API contract (steps are replaced, not patched); clients treat steps as payload data, not stable references.
- **`sync` on categories could detach unexpected pivots** → Mitigation: intentional — the update contract is full-state replacement per the recipe-management spec ("replaces its `Etape` records and pivot associations").
- **Policy auto-discovery assumptions** → Mitigation: verify in tests (`$this->actingAs($other)->put(...)` → 403); if discovery fails, register `Gate::policy` in `AppServiceProvider`.

## Migration Plan

1. New migration `add_statut_to_recettes_table`: `$table->enum('statut', ['published', 'hidden'])->default('published')`; `down()` drops the column. Reversible, additive, no data backfill needed (default covers existing rows).
2. Implementation order: migration → `Recette` model (`statut` fillable, `withPivot('quantity','unit')`, `scopeVisibleTo`) → `RecettePolicy` → Form Requests → `RecetteResource` → `RecetteController` → `routes/api.php` → factory/seeder → tests.
3. Feature branch per GitFlow (e.g. `feature/kan-13-recipe-crud`); deploy is standard Laravel (`php artisan migrate`); rollback = `php artisan migrate:rollback --step=1`.
4. Verification: `php artisan test`, `vendor/bin/pint`, `openspec validate`.

## Open Questions

- **Pagination size** for `GET /api/recipes`: Laravel default 15 for now; the value is a task-level knob that neither the specs nor the approach depend on.
- **`difficulty` controlled vocabulary**: treated as a free-form nullable string (as implemented); an enum/validation list can be added later without spec changes.
