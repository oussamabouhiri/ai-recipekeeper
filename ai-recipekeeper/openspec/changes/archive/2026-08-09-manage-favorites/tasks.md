## 1. Authorization

- [x] 1.1 Create `FavoriPolicy` with `delete(User $user, Favori $favori)` returning `$user->isAdmin() || $user->id === $favori->user_id`
- [x] 1.2 Verify policy auto-discovery and that non-owner removal returns 403

## 2. Validation

- [x] 2.1 Create `StoreFavoriteRequest` with `recette_id` required, integer, `exists:recettes,id`, and `unique:favoris,recette_id,NULL,id,user_id,{auth user id}`

## 3. API Resource

- [x] 3.1 Create `FavoriResource` whitelisting id, user_id, recette_id, created_at, updated_at with nested `recette` via `RecetteResource::make($this->whenLoaded('recette'))`

## 4. Factory

- [x] 4.1 Create `FavoriFactory` with `user_id` and `recette_id` factory defaults

## 5. API Controller

- [x] 5.1 Create `FavoriController` with `index` (paginate current user's favorites with eager-loaded recette), `store` (validate, resolve recipe via `visibleTo` scope with `findOrFail`, create, 201), `destroy` (authorize + delete, 204)
- [x] 5.2 Add `use AuthorizesRequests` trait to `FavoriController`

## 6. Web Controller

- [x] 6.1 Create `FavoriWebController` with `index` (render My Favorites page with eager-loaded recettes), `store` (validate, resolve recipe via `visibleTo` scope, create, redirect back with success message), `destroy` (authorize + delete, redirect back with success message)
- [x] 6.2 Add `use AuthorizesRequests` trait to `FavoriWebController`

## 7. Routes

- [x] 7.1 Register API routes in `routes/api.php` under `auth:sanctum`: `GET /api/favorites`, `POST /api/favorites`, `DELETE /api/favorites/{favori}`
- [x] 7.2 Register web routes in `routes/web.php` under `auth`: `GET /favorites`, `POST /favorites`, `DELETE /favorites/{favori}`

## 8. Blade Views

- [x] 8.1 Create `resources/views/favorites/index.blade.php` extending `layouts/app.blade.php` with favorited recipe cards, view links, remove buttons, and an empty state
- [x] 8.2 Add a "My Favorites" nav link to `resources/views/layouts/app.blade.php`
- [x] 8.3 Pass the current user's favorite for the recipe from `RecipeWebController::show()` and add an add/remove favorite form to `resources/views/recipes/show.blade.php`

## 9. Tests

- [x] 9.1 `FavoritesCrudTest`: user adds favorite 201 with owner attribution; user lists own favorites only; favorite includes recipe; empty list; user removes own favorite 204; recipe deletion cascades favorites; user deletion cascades favorites
- [x] 9.2 `FavoritesAuthorizationTest`: guest add/list/remove returns 401; non-owner removal returns 403; admin removes any favorite
- [x] 9.3 `FavoritesValidationTest`: missing recette_id 422; unknown recette_id 422; duplicate favorite 422; hidden recipe of another user rejected
- [x] 9.4 `FavoritesBladeTest`: guest redirected to login; My Favorites page shows favorited recipes; empty state; toggle state on recipe detail page; remove from My Favorites page redirects with success message

## 10. Verification

- [x] 10.1 Run `php artisan test` and fix failures
- [x] 10.2 Run `vendor/bin/pint` to enforce code style
- [x] 10.3 Run `openspec validate` and confirm the change validates
