## Why

KAN-13 — Recipe CRUD is the first functional feature of AI Recipe Keeper. The database schema and Eloquent models already exist, but there is no controller, route, policy, validation, or test that lets users create, read, update, or delete recipes. Without it, users cannot interact with recipes at all.

The MLD is the source of truth for the database structure. The MLD defines `recettes.statut` as `ENUM('published', 'hidden')` defaulting to `published`, but the implemented migration omits this column. This change closes that gap so recipe visibility can be enforced.

## What Changes

- Add `recettes.statut` column (`ENUM('published','hidden')`, default `'published'`) via a new migration, matching the MLD.
- Add a `RecetteController` exposing the recipe CRUD API under `auth:sanctum` where required.
- Add `RecettePolicy` enforcing owner-or-admin authorization (mirrors the existing `UserPolicy` pattern).
- Add `StoreRecetteRequest` and `UpdateRecetteRequest` Form Requests with validation.
- Add `RecetteFactory` and `RecetteSeeder` for development and tests.
- Persist recipe steps through the existing `Etape` model and `etapes` relationship, and ingredients (pivot `quantity`/`unit`) and categories through the existing pivot relationships, transactionally with the recipe.
- Fix `Recette::ingredients()` to include pivot `quantity` and `unit`.
- Add Feature tests covering CRUD, visibility, authorization, and validation.
- Authenticated users choose `published` or `hidden` when creating/updating their own recipes; no admin-only publishing workflow is introduced.
- The existing `image_path` field is treated as ordinary recipe data only; no image upload/storage functionality is introduced.
- `recettes.instructions` is an existing implementation/schema discrepancy: the column exists in the current Laravel migration and model but is NOT defined in the MLD. It stays explicitly documented as a discrepancy, is left untouched (not created, removed, or renamed), and is NOT part of the KAN-13 API contract. `etapes` is the authoritative structured recipe-step relationship.
- Existing models are not renamed (e.g. `Category`/`Categorie`) as part of this change.
- **Out of scope**: AI generation, queue/jobs, reviews, favorites, dedicated ingredient/category/step management, new database entities, image upload/storage, admin-only publishing workflows, model renames.

## Capabilities

### New Capabilities
- `recipe-management`: Recipe CRUD API (index, store, show, update, destroy), recipe visibility (`published`/`hidden`), authorization rules (owner vs admin), and validation.

### Modified Capabilities
- `database-schema`: add `recettes.statut` column to the recipes table requirement.

## Impact

- **Code**: `app/Http/Controllers/RecetteController.php` (new), `app/Policies/RecettePolicy.php` (new), `app/Http/Requests/StoreRecetteRequest.php` + `UpdateRecetteRequest.php` (new), `app/Http/Resources/RecetteResource.php` (new), `database/factories/RecetteFactory.php` (new), `database/seeders/RecetteSeeder.php` (new), `routes/api.php` (recipe routes), `app/Models/Recette.php` (add `statut` to fillable/casts, `withPivot` on `ingredients()`, visibility scope).
- **Database**: one new migration adding `recettes.statut`.
- **API**: new `GET/POST /api/recipes`, `GET/PUT/DELETE /api/recipes/{recipe}` endpoints.
- **Tests**: `tests/Feature/RecipeCrudTest.php`, `tests/Feature/RecipeVisibilityTest.php`, `tests/Feature/RecipeAuthorizationTest.php`, `tests/Feature/RecipeValidationTest.php` (new).
