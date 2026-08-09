## Why

Users can browse recipes but cannot bookmark them. The `favoris` table, the `Favori` model, and the `User`/`Recette` relationships already exist with a unique constraint and cascade deletes, yet no API, route, page, or authorization layer exposes favorite functionality.

## What Changes

- Add a `FavoriController` exposing favorite management through a REST API and a Blade web interface.
- Add a `FavoriPolicy` enforcing owner-or-admin authorization for removing favorites.
- Add a `FavoriResource` for API responses.
- Add a `FavoriFactory` for development and tests.
- Add a "My Favorites" Blade page at `/favorites` listing the authenticated user's favorited recipes, with remove and view actions, plus an empty state.
- Add a favorite toggle action on the recipe detail page (add/remove).
- Add a "My Favorites" link to the authenticated app layout navbar.
- Duplicate favorites are prevented by the existing unique constraint on `(user_id, recette_id)` plus validation; a duplicate add returns 422.
- Deleting a recipe or a user removes their favorites automatically through the existing cascade delete behavior — no code changes needed.
- Only recipes the current user is authorized to view can be favorited (hidden recipes of other users cannot be favorited).
- **Out of scope**: AI functionality, recipe CRUD modifications, reviews, favorite counts/aggregations, sharing favorites, sorting/filtering favorites beyond listing order, schema changes (the `favoris` table already satisfies the requirements).

## Capabilities

### New Capabilities
- `favorites`: Favorite management — API and Blade endpoints to list, add, and remove favorites, authorization rules, duplicate prevention, and cascade behavior.

### Modified Capabilities
- `authorization`: Add `FavoriPolicy` to the policy foundation — removing a favorite requires the favorite owner or an admin.
- `recipe-blade-ui`: The recipe detail page gains a favorite toggle button for authenticated users (no other recipe UI changes).

## Impact

- **Code**: `app/Http/Controllers/FavoriController.php` (new), `app/Policies/FavoriPolicy.php` (new), `app/Http/Resources/FavoriResource.php` (new), `database/factories/FavoriFactory.php` (new), `resources/views/favorites/` (new Blade views), `resources/views/recipes/show.blade.php` (favorite toggle button), `resources/views/layouts/app.blade.php` (nav link), `routes/web.php` and `routes/api.php` (favorite routes).
- **Database**: no new migrations — the `favoris` table already exists with the unique constraint and cascade deletes.
- **API**: new `GET /api/favorites` (authenticated), `POST /api/favorites` (authenticated), `DELETE /api/favorites/{favori}` (authenticated, owner or admin).
- **Blade**: new `favorites/index.blade.php`; favorite toggle on `recipes/show.blade.php`.
- **Tests**: `tests/Feature/FavoritesCrudTest.php`, `tests/Feature/FavoritesAuthorizationTest.php`, `tests/Feature/FavoritesValidationTest.php`, `tests/Feature/FavoritesBladeTest.php`.
