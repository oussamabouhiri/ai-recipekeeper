## Why

Users can browse, manage, and favorite recipes but cannot rate or review them. The `avis` table, the `Avis` model, and the `User`/`Recette` relationships already exist with cascade deletes and indexes, yet no API, route, page, or authorization layer exposes review functionality.

## What Changes

- Add an `AvisController` exposing reviews through a REST API and an `AvisWebController` for the Blade interface.
- Add an `AvisPolicy` enforcing owner-or-admin authorization for updating and deleting reviews.
- Add an `AvisResource` for API responses and an `AvisFactory` for development and tests.
- Add review endpoints nested under recipes: list and create via `GET/POST /api/recipes/{recipe}/reviews`, update and delete via `PUT/DELETE /api/reviews/{avis}`.
- Add web routes mirroring the API: `POST /recipes/{recipe}/reviews`, `PUT /reviews/{avis}`, `DELETE /reviews/{avis}`.
- Add a reviews section to the recipe detail page showing the average rating, the review count, the list of reviews, a create form, and edit/delete actions for authorized users.
- Enforce one review per user per recipe through validation (scoped uniqueness on `(user_id, recette_id)`); a duplicate submission returns 422. No new migration: the existing `avis` table has no unique constraint, and the database design must not change.
- Reviews are restricted to rating 1–5 (integer) with an optional comment (max 1000 characters).
- Users may review their own recipes.
- Deleting a recipe or a user removes their reviews automatically through the existing cascade deletes — no code changes needed.
- Only recipes the current user is authorized to view can be reviewed; hidden recipes of other users respond as if they do not exist.
- Review aggregates (average rating and count) are exposed by the reviews list endpoint and rendered on the recipe detail Blade page. The existing recipe API contract is untouched.
- **Out of scope**: AI functionality, recipe CRUD modifications, favorites changes, new database tables or columns, review pagination customization, dashboard changes, admin-only review pages.

## Capabilities

### New Capabilities
- `reviews-ratings`: Review management — API and Blade endpoints to list, create, update, and delete reviews, one-review-per-user enforcement, rating/comment validation, visibility rules, and rating aggregates (average and count).

### Modified Capabilities
- `authorization`: Add `AvisPolicy` to the policy foundation — updating or deleting a review requires the review owner or an admin.
- `recipe-blade-ui`: The recipe detail page gains a reviews section (average rating, review count, review list, create/edit/delete forms) for authorized users; no other recipe UI changes.

## Impact

- **Code**: `app/Http/Controllers/AvisController.php` (new), `app/Http/Controllers/AvisWebController.php` (new), `app/Policies/AvisPolicy.php` (new), `app/Http/Resources/AvisResource.php` (new), `app/Http/Requests/StoreAvisRequest.php` (new), `app/Http/Requests/UpdateAvisRequest.php` (new), `database/factories/AvisFactory.php` (new), `resources/views/recipes/show.blade.php` (reviews section), `routes/web.php` and `routes/api.php` (review routes).
- **Database**: no new migrations — the `avis` table already exists with cascade deletes and indexes.
- **API**: new `GET /api/recipes/{recipe}/reviews` (visibility-gated, includes rating aggregate), `POST /api/recipes/{recipe}/reviews` (authenticated), `PUT /api/reviews/{avis}` (owner or admin), `DELETE /api/reviews/{avis}` (owner or admin).
- **Blade**: reviews section on `recipes/show.blade.php`.
- **Tests**: `tests/Feature/ReviewsCrudTest.php`, `tests/Feature/ReviewsAuthorizationTest.php`, `tests/Feature/ReviewsValidationTest.php`, `tests/Feature/ReviewsBladeTest.php`.
