## 1. CategorySeeder Expansion

- [x] 1.1 Expand CategorySeeder from 6 to 12 categories (add: Entrées froides, Soupes, Salades, Pâtisseries, Accompagnements, Plats de résistance)

## 2. IngredientSeeder

- [x] 2.1 Create IngredientSeeder with 40-50 real French cuisine ingredients (proteins, vegetables, herbs, spices, dairy, pantry staples)

## 3. RealRecipeSeeder

- [x] 3.1 Create RealRecipeSeeder with 25-30 real French recipes (Coq au Vin, Ratatouille, Tarte Tatin, Bouillabaisse, etc.)
- [x] 3.2 Each recipe has: title, description, prep_time, cook_time, servings, difficulty, is_ai_generated=false, statut=published
- [x] 3.3 Each recipe has 3-8 real cooking steps with step_number and instruction
- [x] 3.4 Each recipe has 3-8 ingredients with realistic quantity and unit on pivot
- [x] 3.5 Each recipe has 1-3 category associations
- [x] 3.6 Use firstOrCreate pattern for idempotent seeding

## 4. DatabaseSeeder Update

- [x] 4.1 Remove old RecetteSeeder call from DatabaseSeeder
- [x] 4.2 Add IngredientSeeder call before RealRecipeSeeder
- [x] 4.3 Add RealRecipeSeeder call after IngredientSeeder
- [x] 4.4 Verify seeding order: CategorySeeder → IngredientSeeder → RealRecipeSeeder

## 5. Verification

- [x] 5.1 Run `php artisan migrate:fresh --seed` and verify no errors
- [x] 5.2 Verify 12 categories exist
- [x] 5.3 Verify 40-50 ingredients exist
- [x] 5.4 Verify 25-30 recipes exist with correct relationships
- [x] 5.5 Run `php artisan test` to ensure seeders don't break existing tests
