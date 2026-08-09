## Context

See proposal.md - Why for motivation. The app is a Laravel 12/13 project (SQLite for tests, MySQL in production) with Sanctum bearer-token auth already in place. The database schema and Eloquent models already exist:

- `categories` table (name, description, timestamps) with `Category` model having `HasFactory` trait and `recettes()` belongsToMany relationship.
- `recette_categorie` pivot table (recette_id, categorie_id) with cascade delete on both foreign keys.
- `User::isAdmin()`, `UserPolicy`, `RecettePolicy`, and admin middleware already exist.
- No category controller, policy, Form Request, resource, factory, or seeder exists.
- Recipe CRUD is API-only; web routes are only for auth and a placeholder admin page.
- Bootstrap 5.3.3 is used for Blade views with consistent patterns (cards, forms, validation).

Constraints from the approved proposal: categories are global/shared (no owner/user association); admin-only for create/update/delete; public/authenticated for listing/viewing; cascade delete on pivot; no AI functionality; no recipe CRUD modifications; no category hierarchy.

## Goals / Non-Goals

**Goals:**
- REST surface: `GET /api/categories` (public), `GET /api/categories/{category}` (public), `POST /api/categories` (admin), `PUT /api/categories/{category}` (admin), `DELETE /api/categories/{category}` (admin).
- Web routes under admin middleware for category management Blade views.
- Policy-based authorization for create/update/delete (admin-only), mirroring `UserPolicy` pattern.
- Validation via Form Requests with unique name constraint.
- CategoryResource for API responses.
- Feature tests for CRUD, authorization, validation, and cascade behavior.
- Blade views for admin category management following existing UI patterns.

**Non-Goals:**
- Category ownership/user association (categories are global).
- Category ordering/sorting or hierarchy/nesting.
- AI functionality.
- Recipe CRUD modifications.
- Image upload/storage for categories.
- Category search or filtering.

## Decisions

### Decision: Admin-only for mutations, public for reads
**Choice**: `CategoryPolicy` with `create`, `update`, `delete` returning `$user->isAdmin()`; listing/viewing accessible to all (guest and authenticated).
**Rationale**: Categories are global shared resources, not user-owned. Admin-only management matches the existing admin middleware pattern and ensures data quality. Public listing allows recipe creation forms to display available categories without requiring authentication.
**Alternatives**: Authenticated-only for all operations (rejected: prevents guests from seeing categories); owner-based authorization (rejected: categories have no owner).

### Decision: API routes under auth:sanctum for admin operations
**Choice**: `POST/PUT/DELETE /api/categories` inside `auth:sanctum` middleware group; `GET /api/categories` and `GET /api/categories/{category}` public.
**Rationale**: Follows the existing recipe API pattern where read endpoints are public and write endpoints require authentication. Admin authorization happens in the controller via Policy.
**Alternatives**: Separate admin API prefix (rejected: adds complexity with no benefit); all endpoints public (rejected: no authorization).

### Decision: Web routes under auth + admin middleware for Blade views
**Choice**: Category management web routes in `routes/web.php` inside `auth` → `admin` middleware chain, following the existing `/admin` route pattern.
**Rationale**: The existing admin page uses this pattern; category management is admin-only functionality. Blade views provide a simple UI without JavaScript frameworks.
**Alternatives**: API-only with JavaScript frontend (rejected: adds complexity; project uses Blade for auth views); separate admin subdomain (rejected: over-engineering for this stage).

### Decision: CategoryResource for API responses
**Choice**: A `CategoryResource` that returns `id, name, description, created_at, updated_at` plus nested `recettes` (when loaded) using `whenLoaded()`.
**Rationale**: Follows the existing `RecetteResource` pattern; explicit field whitelisting ensures consistency and prevents accidental field leakage.
**Alternatives**: Return model as-is (rejected: no control over shape); inline array mapping (rejected: verbose, duplicated across controller methods).

### Decision: Full-replacement semantics on update
**Choice**: Update the category row directly (name, description); no pivot manipulation needed since associations are managed by recipe CRUD.
**Rationale**: Categories are simple entities with no child rows or complex relationships. Pivot associations are managed by `RecetteController::syncRelations()`, not by category management.
**Alternatives**: Transaction wrapping (rejected: unnecessary for single-row update); pivot sync on update (rejected: category management should not touch recipe associations).

### Decision: Unique name validation
**Choice**: `name` required, string, max:255, unique on categories table (ignoring current record on update).
**Rationale**: Category names should be unique to prevent confusion and duplication. The `unique` rule with `ignore` on update follows standard Laravel validation patterns.
**Alternatives**: Non-unique names (rejected: leads to confusing duplicate categories); soft delete with unique scope (rejected: adds complexity for no requirement).

### Decision: Cascade delete on pivot, not on recipes
**Choice**: Deleting a category removes `recette_categorie` pivot rows via cascade (already defined in migration), but does NOT delete or modify the recipes themselves.
**Rationale**: The existing migration defines `cascadeOnDelete` on both foreign keys in `recette_categorie`. Deleting a category removes the association, not the recipe. This is the expected behavior — recipes should survive category deletion.
**Alternatives**: Restrict delete when attached to recipes (rejected: adds complexity; cascade is already defined); detach only (rejected: same as cascade, which is what happens).

### Decision: Blade views follow existing patterns
**Choice**: Bootstrap 5 cards, forms with `@error` validation, `@extends`/`@section` template inheritance, dark navbar for admin area. Views in `resources/views/categories/`.
**Rationale**: Follows the established `layouts/guest.blade.php` and `admin/index.blade.php` patterns. Consistent UI reduces cognitive load and maintenance burden.
**Alternatives**: Separate admin layout (rejected: over-engineering for 3 views); Tailwind (rejected: project uses Bootstrap 5 per config).

### Decision: CategoryFactory with minimal defaults
**Choice**: `CategoryFactory` with `name` faker and nullable `description`; no `afterCreating` wiring.
**Rationale**: Keeps the factory deterministic; tests create categories explicitly per scenario. No complex relationships to wire.
**Alternatives**: Factory with random related data (rejected: nondeterministic tests); no factory (rejected: tests need consistent category creation).

## Risks / Trade-offs

- **Duplicate category names** → Mitigation: unique validation rule prevents duplicates; tests assert 422 on duplicate name.
- **Cascade delete removing unexpected pivots** → Mitigation: intentional — the migration defines cascade delete; deleting a category removes associations, not recipes; tests verify recipes survive category deletion.
- **Admin-only management blocks non-admin users from creating categories** → Mitigation: intentional — categories are global shared resources that require quality control; non-admin users can still attach existing categories to recipes via recipe CRUD.
- **No category ordering/sorting** → Mitigation: accepted for this stage; alphabetical ordering can be added later without spec changes.
- **Blade views require separate web routes from API** → Mitigation: accepted — web routes under admin middleware for Blade, API routes under auth:sanctum for JSON; no conflict since they serve different clients.
- **CategoryResource returns all fields including timestamps** → Mitigation: acceptable for admin management; timestamps provide useful metadata for debugging and auditing.

## Migration Plan

1. No new migrations — the `categories` table and `recette_categorie` pivot already exist with the correct schema.
2. Implementation order: CategoryPolicy → Form Requests → CategoryResource → CategoryController → routes (web + API) → factory/seeder → Blade views → tests.
3. Feature branch per GitFlow (e.g. `feature/manage-categories`); deploy is standard Laravel (`php artisan migrate` — no new migrations needed); rollback is code-only revert.
4. Verification: `php artisan test`, `vendor/bin/pint`, `openspec validate`.

## Open Questions

- **Pagination size** for `GET /api/categories`: Laravel default 15 for now; the value is a task-level knob that neither the specs nor the approach depend on.
- **Category search/filter**: not in scope; can be added later without spec changes.
