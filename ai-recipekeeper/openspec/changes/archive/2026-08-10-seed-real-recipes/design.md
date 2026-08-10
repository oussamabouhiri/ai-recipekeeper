## Context

Current seeders: `CategorySeeder` (6 categories), `RecetteSeeder` (creates duplicate categories + 5 factory recipes with fake data), `AdminUserSeeder` (exists but not called). The `DatabaseSeeder` uses `WithoutModelEvents` and calls both seeders. Models: `Recette`, `Category`, `Ingredient` with pivot tables `recette_categorie` and `recette_ingredient` (quantity, unit).

## Goals / Non-Goals

**Goals:**
- Populate the app with 25-30 real, appealing French recipes
- Provide 12 categories covering all recipe types
- Provide 40-50 real ingredients (proteins, vegetables, herbs, spices, dairy)
- Each recipe has real titles, descriptions, 3-8 steps, 3-8 ingredients with realistic quantities
- All seeded recipes are `published` status so everyone can see them

**Non-Goals:**
- No image uploads (recipes have `image_path` = null)
- No user-generated content during seeding
- No API changes
- No migration changes

## Decisions

1. **Replace `RecetteSeeder` entirely** — Current one creates duplicate categories and uses factories. New `RealRecipeSeeder` will use hardcoded real data.

2. **Separate `IngredientSeeder`** — Keep ingredients in their own seeder for maintainability. ~50 ingredients covering French cuisine basics.

3. **Hardcode all recipe data** — Use PHP arrays with real French recipe data instead of factories. Each recipe is hand-crafted with proper titles, descriptions, and steps.

4. **Keep `CategorySeeder` separate** — Expand from 6 to 12 categories. Don't merge with recipe seeder.

5. **Assign all recipes to one test user** — All 25-30 recipes belong to `test@example.com` user for simplicity.

## Risks / Trade-offs

- **Seeder file size** — 30 recipes with full data = large PHP file. Mitigation: Organize with clear array structure, one recipe per entry.
- **Data accuracy** — Real recipes must have realistic cooking times, quantities, and steps. Mitigation: Use well-known French recipes with verified proportions.
- **Idempotency** — Running `php artisan db:seed` twice creates duplicates. Mitigation: Use `Recette::where('title', $title)->firstOrCreate()` pattern.
