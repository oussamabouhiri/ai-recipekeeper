## Why

The RecipeKeeper app currently has only 5 factory-generated recipes with random titles like "Odit et velit est et voluptatem" and generic steps like "Préparer tous les ingrédients." This makes the app feel empty and uninviting. New users see no value immediately. The app needs 25-30 real, appealing French recipes with proper ingredients, steps, and categories to demonstrate functionality and provide a useful starting point.

## What Changes

- **Replace** `RecetteSeeder` with `RealRecipeSeeder` containing 25-30 real French recipes
- **Expand** `CategorySeeder` from 6 to 12 categories (add: Entrées froides, Soupes, Salades, Pâtisseries, Accompagnements, Plats de résistance)
- **Add** `IngredientSeeder` with 40-50 real ingredients (herbs, spices, proteins, vegetables, dairy)
- **Update** `DatabaseSeeder` to call the new seeders in correct order
- **Fix** duplicate category creation in current `RecetteSeeder`

## Capabilities

### New Capabilities

None — this is a data-seeding change with no spec-level behavior changes.

### Modified Capabilities

None — existing CRUD behavior remains unchanged.

## Impact

- **Files modified**: `database/seeders/DatabaseSeeder.php`, `database/seeders/CategorySeeder.php`
- **Files replaced**: `database/seeders/RecetteSeeder.php` → `database/seeders/RealRecipeSeeder.php`
- **Files added**: `database/seeders/IngredientSeeder.php`
- **No API changes**: All existing endpoints continue working
- **No migrations**: Uses existing tables and models
- **Database**: Seeding adds ~12 categories, ~50 ingredients, ~30 recipes with steps and pivot data
