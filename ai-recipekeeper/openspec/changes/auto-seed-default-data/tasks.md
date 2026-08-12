## 1. Entrypoint auto-seed

- [x] 1.1 Add a check in `docker/php/entrypoint.sh` that queries `Recette::count()` after migrations and stores the result
- [x] 1.2 After the migration block, add a conditional block: if recipe count is 0, run `php artisan db:seed --force`
- [x] 1.3 Inside the same conditional block, run `php artisan recipe:sync-images` after seeding
- [x] 1.4 Ensure the seeding steps are inside the existing `RUN_MIGRATIONS` guard so the queue-worker never triggers them

## 2. Orphan image cleanup

- [x] 2.1 Extend `SyncRecipeImages::handle()` to delete known orphan files (`dfasfds.jpg`, `hello-recipe-test.png`, `hello-world.jpg`, `traditional-moroccan-goat-tajine-sossi.webp`) from `public/images/recipes/` before linking images
- [x] 2.2 Verify no recipe row references the removed filenames by querying `recettes.image_path`

## 3. Verification

- [x] 3.1 Run `./sail artisan migrate && ./sail artisan db:seed --force && ./sail artisan recipe:sync-images` on an empty database and verify 26 recipes, 12 categories, and 26 images present
- [x] 3.2 Restart the container (`docker compose restart app`) and verify seeding is skipped — recipe count remains 26, no duplicates
- [x] 3.3 Start the app (`docker compose up`) and verify the browse page renders recipe cards with images (HTTP 200 for image URLs)
- [x] 3.4 Verify the 4 orphan files are removed from `public/images/recipes/`
- [x] 3.5 Create a user account, add a recipe, restart the container — verify the user recipe is preserved
