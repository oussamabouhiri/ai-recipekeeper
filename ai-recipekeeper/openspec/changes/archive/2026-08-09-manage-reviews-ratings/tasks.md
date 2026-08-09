## 1. Authorization

- [x] 1.1 Create `AvisPolicy` with `update(User $user, Avis $avis)` and `delete(User $user, Avis $avis)` both returning `$user->isAdmin() || $user->id === $avis->user_id`
- [x] 1.2 Verify policy auto-discovery and that non-owner update/delete returns 403

## 2. Validation

- [x] 2.1 Create `StoreAvisRequest` with `rating` required, integer, between 1 and 5 and `comment` nullable, string, max 1000
- [x] 2.2 Add a validator after-hook to `StoreAvisRequest` rejecting the request with a 422 when the authenticated user already has an `Avis` for the route-bound recipe
- [x] 2.3 Create `UpdateAvisRequest` with the same `rating`/`comment` rules but no uniqueness after-hook

## 3. API Resource

- [x] 3.1 Create `AvisResource` whitelisting id, recette_id, user_id, rating, comment, created_at, updated_at with nested `user` as `{id, name}` via `whenLoaded('user')`

## 4. Factory

- [x] 4.1 Create `AvisFactory` with `user_id` and `recette_id` factory defaults, a random 1–5 `rating`, and a sentence `comment`

## 5. API Controller

- [x] 5.1 Create `AvisController` with `index` (resolve recipe via `visibleTo` scope with `findOrFail`, compute `rating_avg`/`rating_count` via `withAvg`/`withCount`, return paginated reviews with authors and the aggregates via `additional`), `store` (resolve recipe via `visibleTo`, create with owner attribution, 201), `update` (authorize + update), `destroy` (authorize + delete, 204)
- [x] 5.2 Add `use AuthorizesRequests` trait to `AvisController`

## 6. Web Controller

- [x] 6.1 Create `AvisWebController` with `store` (resolve recipe via `visibleTo` scope, create with owner attribution, redirect back with success message), `update` (authorize + update, redirect back with success message), `destroy` (authorize + delete, redirect back with success message)
- [x] 6.2 Add `use AuthorizesRequests` trait to `AvisWebController`

## 7. Routes

- [x] 7.1 Register API routes in `routes/api.php`: `GET /api/recipes/{recipe}/reviews` (public), and under `auth:sanctum` `POST /api/recipes/{recipe}/reviews`, `PUT /api/reviews/{avis}`, `DELETE /api/reviews/{avis}`
- [x] 7.2 Register web routes in `routes/web.php` under `auth`: `POST /recipes/{recipe}/reviews`, `PUT /reviews/{avis}`, `DELETE /reviews/{avis}`

## 8. Blade UI

- [x] 8.1 Extend `RecipeWebController::show()` to eager-load reviews with authors, compute the current user's review, the average rating (rounded to 1 decimal) and the review count, and pass them to the view
- [x] 8.2 Add a reviews card to `resources/views/recipes/show.blade.php` with the average rating, review count, review list with author names, a create form (when the user has no review), an edit form and delete button (for the user's own review), and a delete button on every review for admins

## 9. Tests

- [x] 9.1 `ReviewsCrudTest`: user creates a review 201 with owner attribution; user reviews own recipe; user updates own review; user deletes own review 204; two users review the same recipe; recipe reviews listed with author names; empty review list; recipe deletion cascades reviews; user deletion cascades reviews
- [x] 9.2 `ReviewsAuthorizationTest`: guest create/update/delete returns 401; guest can list reviews of a visible recipe; non-owner update returns 403; non-owner delete returns 403; admin updates and deletes any review; hidden recipe of another user returns 404 for list and create
- [x] 9.3 `ReviewsValidationTest`: missing rating 422; rating out of range (0, 6, non-integer) 422; comment over 1000 characters 422; empty comment creates a review with null comment; duplicate review for the same recipe 422; owner can review own hidden recipe
- [x] 9.4 `ReviewsAggregationTest`: list response includes `rating_avg` (rounded to 1 decimal) and `rating_count`; recipe without reviews returns null average and zero count
- [x] 9.5 `ReviewsBladeTest`: guest submitting any review form is redirected to login; detail page shows average, count, and reviews; create form shown when user has no review; edit/delete actions shown for the user's own review; admin sees delete on every review; successful create/update/delete redirect back with success message; invalid form redisplays with validation errors
- [x] 9.6 Confirm existing tests (`RecipeApiContractTest`, `RecipeBladeTest`, favorites tests) still pass unchanged

## 10. Verification

- [x] 10.1 Run `php artisan test` and fix failures
- [x] 10.2 Run `vendor/bin/pint` to enforce code style
- [x] 10.3 Run `openspec validate` and confirm the change validates
