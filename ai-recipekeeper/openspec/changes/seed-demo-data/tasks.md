## 1. Recipe data

- [x] 1.1 Rename the source image `Images/imagesRecipe/honey-glazed-salamon-with-roasted-carrots.png` to `saumon-glace-au-miel-et-carottes-roties.png` (slug must equal `Str::slug('Saumon Glacé au Miel et Carottes Rôties')`)
- [x] 1.2 Add the recipe method `saumonGlaceAuMielEtCarottesRoties()` to `RealRecipeSeeder` (title, description, prep_time, cook_time, servings, difficulty, categories, ingredients with quantity/unit, steps) reusing ingredient names from `IngredientSeeder` (Saumon, Carottes, Miel, etc.)
- [x] 1.3 Register the new recipe in `RealRecipeSeeder::getRecipes()`
- [ ] 1.4 Run the sync command and confirm no recipe is skipped (every recipe gets an `image_path`)

## 2. Seeding flow

- [ ] 2.1 Document the native seeding flow in the README: `php artisan migrate` → `php artisan db:seed --force` → `php artisan recipe:sync-images`
- [ ] 2.2 Verify the flow is repeatable: a second `db:seed` + `recipe:sync-images` run creates no duplicates and copies no files twice

## 3. Cleanup

- [ ] 3.1 Remove the 4 orphan test images from `public/images/recipes/`: `dfasfds.jpg`, `hello-recipe-test.png`, `hello-world.jpg`, `traditional-moroccan-goat-tajine-sossi.webp`
- [ ] 3.2 Confirm no recipe row references the removed filenames (query `recettes` for `image_path` matches)

## 4. Verification

- [ ] 4.1 Seed the local MySQL database and verify 26 recipes, 12 categories, ingredients, and images linked (`image_path` non-null, 26 files in `public/images/recipes/`)
- [ ] 4.2 Start the app (`php artisan serve`) and verify the browse page and recipe show page render images (HTTP 200, no broken `<img>`)
- [ ] 4.3 Re-run the seeding flow and verify no duplicates are created
- [ ] 4.4 Verify the new recipe « Saumon Glacé au Miel et Carottes Rôties » is present, categorized, and shows its image
- [ ] 4.5 Commit and push the change (seeder, README, renamed image, removed orphans)
