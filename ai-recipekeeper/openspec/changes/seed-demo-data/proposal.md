## Why

A fresh `docker compose up` produces an empty application: the entrypoint only runs migrations, never seeds. The browse page, dashboard, categories and favorites are blank until someone manually seeds — and even then, `RealRecipeSeeder` leaves `image_path` empty (25 recipe images exist in `Images/imagesRecipe/` but are never linked). The 26th image (`honey-glazed-salamon-with-roasted-carrots.png`) has no recipe at all, and its filename contains a typo.

## What Changes

- The 26th recipe « Saumon Glacé au Miel et Carottes Rôties » is added to `RealRecipeSeeder` (ingredients, steps, categories, times, difficulty) and the source image is renamed to `saumon-glace-au-miel-et-carottes-roties.png` so the slug matches the recipe title.
- The demo seeding flow is documented and executable natively (no docker on this branch): `php artisan db:seed --force` then `php artisan recipe:sync-images` produce a fully populated app on an empty database. Seeding is idempotent: re-running on an already-seeded database changes nothing.
- `recipe:sync-images` copies the 26 recipe images to `public/images/recipes/` and populates `recettes.image_path`.
- Orphan test images are removed from `public/images/recipes/` (`dfasfds.jpg`, `hello-recipe-test.png`, `hello-world.jpg`, `traditional-moroccan-goat-tajine-sossi.webp`).
- Seeding remains an explicit manual step; there is no auto-seed on boot on this branch (no docker entrypoint — a follow-up can add it when the docker stack lands).

## Capabilities

### New Capabilities

- `demo-data`: automatic seeding of demo categories, ingredients, and recipes with linked images when the database is empty, plus the image-sync flow (`recipe:sync-images`).

### Modified Capabilities

- `database-schema`: no requirement changes — the `recettes.image_path` column already exists; only data is populated.

## Impact

- `database/seeders/RealRecipeSeeder.php` — new honey-glazed salmon recipe method (title, description, ingredients, steps, categories).
- `Images/imagesRecipe/` — one image renamed (`honey-glazed-salamon-with-roasted-carrots.png` → `saumon-glace-au-miel-et-carottes-roties.png`).
- `public/images/recipes/` — 4 orphan test images removed; 26 canonical images present after sync.
- `README.md` — seeding flow documented (migrate → `db:seed` → `recipe:sync-images`).
- `app/Console/Commands/SyncRecipeImages.php` — already implemented; reused as-is.
- No composer/npm dependency changes.
