## Why

New users who sign up see an empty browse page ("No recipes yet") because the Docker entrypoint only runs `php artisan migrate` and never seeds the database. The 26 demo recipes, 12 categories, and ingredient library exist in seeders (`RealRecipeSeeder`, `CategorySeeder`, `IngredientSeeder`) but are never executed on fresh installs. Additionally, the `SyncRecipeImages` command exists but is never called automatically, so even if seeding ran, recipe images wouldn't be linked. This creates a dead first-use experience — the app looks broken until someone manually runs `php artisan db:seed --force && php artisan recipe:sync-images`.

## What Changes

- The Docker entrypoint (`docker/php/entrypoint.sh`) is extended to run `php artisan db:seed --force` and `php artisan recipe:sync-images` after migrations, but only when the database is empty (no recipes exist yet).
- The `RealRecipeSeeder` already defines 26 complete French recipes with ingredients, steps, and categories — no new recipe data is added; this change wires the existing seeder into the startup flow.
- A guard ensures seeding is non-destructive: if recipes already exist (e.g. user-created data), seeding is skipped entirely.
- The 4 orphan test images in `public/images/recipes/` (`dfasfds.jpg`, `hello-recipe-test.png`, `hello-world.jpg`, `traditional-moroccan-goat-tajine-sossi.webp`) are removed as cleanup.

## Capabilities

### New Capabilities

- `demo-data`: Automatic seeding of default categories, ingredients, and 26 recipes with linked images when the database is empty, triggered by the Docker entrypoint on fresh installs.

### Modified Capabilities

- `browse-recipes`: No requirement changes — the `visibleTo` scope already shows all `published` recipes to all users. The only change is that seeded recipes now exist in the database, so the browse page is no longer empty.

## Impact

- `docker/php/entrypoint.sh` — add seeding + image sync steps after migration
- `database/seeders/RealRecipeSeeder.php` — no changes needed (already complete with 26 recipes)
- `database/seeders/CategorySeeder.php` — no changes needed (12 categories)
- `database/seeders/IngredientSeeder.php` — no changes needed (full ingredient list)
- `app/Console/Commands/SyncRecipeImages.php` — no changes needed (already works)
- `public/images/recipes/` — 4 orphan files removed; 26 canonical images populated on first boot
- No composer/npm dependency changes
- No model or migration changes
