## 1. Database Migration

- [x] 1.1 Create migration `add_statut_to_recettes_table` adding `statut` enum (`published`/`hidden`) with default `published`, reversible via `down()`
- [x] 1.2 Run `php artisan migrate` and verify with `php artisan migrate:status`
- [x] 1.3 Verify reversibility: `php artisan migrate:rollback --step=1` then re-migrate

## 2. Recette Model

- [x] 2.1 Add `statut` to `Recette::$fillable`
- [x] 2.2 Add `withPivot('quantity', 'unit')` to `Recette::ingredients()`
- [x] 2.3 Add `scopeVisibleTo(?User $user)` implementing visibility (published to all; hidden to owner and admins; admins see everything)
- [x] 2.4 Confirm the existing `etapes` hasMany relationship is used as-is (no `Etape` changes)

## 3. Authorization

- [x] 3.1 Create `RecettePolicy` with `update`/`delete` owner-or-admin checks mirroring `UserPolicy`
- [x] 3.2 Verify policy auto-discovery (or register via `Gate::policy`) and that non-owner update/delete returns 403

## 4. Validation

- [x] 4.1 Create `StoreRecetteRequest` (title required; statut `in:published,hidden`; non-negative prep_time/cook_time; servings `min:1`; `etapes` array of `{step_number min:1, instruction required}`; `ingredients` array of `{ingredient_id exists, quantity, unit}`; `categories` array of existing ids)
- [x] 4.2 Create `UpdateRecetteRequest` with the same rules; ensure `user_id` and `is_ai_generated` are never accepted

## 5. Controller, Resource & Routes

- [x] 5.1 Create `RecetteResource` whitelisting MLD fields (id, title, description, prep_time, cook_time, servings, difficulty, image_path, statut, is_ai_generated, user_id, timestamps) plus nested user, etapes (ordered by step_number), ingredients (with pivot quantity/unit), and categories — explicitly excluding `instructions`
- [x] 5.2 Create `RecetteController` with `index` (visibleTo + eager loads + paginate), `store` (transaction, owner attribution, default statut), `show` (visibility, 404 for hidden non-owner), `update` (authorize + transaction: replace etapes, sync categories/ingredients), `destroy` (authorize + delete)
- [x] 5.3 Register routes in `routes/api.php`: public `GET /api/recipes` and `GET /api/recipes/{recipe}`; `auth:sanctum` `POST /api/recipes`, `PUT /api/recipes/{recipe}`, `DELETE /api/recipes/{recipe}`

## 6. Factory & Seeder

- [x] 6.1 Create `RecetteFactory` (default `statut` `published`, `hidden()` state)
- [x] 6.2 Create `RecetteSeeder` creating recipes with etapes, ingredient pivots (quantity/unit), and categories; register in `DatabaseSeeder`

## 7. Tests

- [x] 7.1 `RecipeCrudTest`: guest list returns only published; guest create 401; authenticated create 201 with etapes/pivots persisted and owner attribution; show published; update/delete by owner; guest update/delete 401
- [x] 7.2 `RecipeVisibilityTest`: listing for guest, owner, other user, and admin; hidden recipe show → 404 for non-owner; default statut `published`; owner can choose `published`/`hidden` without admin action
- [x] 7.3 `RecipeAuthorizationTest`: owner and admin can update/delete; non-owner gets 403
- [x] 7.4 `RecipeValidationTest`: missing title, invalid statut, unknown ingredient id, invalid etape (missing/zero step_number or missing instruction), negative prep/cook time, servings below 1 → 422
- [x] 7.5 Assert `instructions` never appears in store/show responses or accepted payloads (API contract check)

## 8. Verification

- [x] 8.1 Run `php artisan test` and fix failures
- [x] 8.2 Run `vendor/bin/pint` to enforce code style
- [x] 8.3 Run `openspec validate` and confirm the change validates
