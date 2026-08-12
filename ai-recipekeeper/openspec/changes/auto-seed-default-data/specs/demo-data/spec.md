## Purpose

Provides a fully populated demo experience for the RecipeKeeper app: when the database is empty (fresh install), the system automatically seeds default categories, ingredients, and 26 recipes with linked images so every user sees a populated browse page from first launch.

## ADDED Requirements

### Requirement: Automatic seeding on fresh database
The system SHALL automatically seed demo data (categories, ingredients, recipes with steps) when the database contains no recipes, without requiring manual intervention.

#### Scenario: Fresh database triggers seeding
- **WHEN** the application starts and the `recettes` table is empty
- **THEN** the system runs the database seeders (`CategorySeeder`, `IngredientSeeder`, `RealRecipeSeeder`) to populate 12 categories, the ingredient library, and 26 French recipes with steps and category associations

#### Scenario: Seeding is skipped when data exists
- **WHEN** the application starts and the `recettes` table already contains rows
- **THEN** the system skips seeding entirely and does not create duplicate data

#### Scenario: Seeding is idempotent
- **WHEN** the seeding flow is run a second time on a database that already contains the demo data
- **THEN** no duplicate categories, ingredients, or recipes are created and existing user-created recipes are preserved

### Requirement: Recipe images are linked after seeding
The system SHALL copy recipe image files to the public directory and update each recipe's `image_path` so images render on recipe cards and detail pages.

#### Scenario: Images are synced after seeding
- **WHEN** demo seeding has created the 26 recipes
- **THEN** each recipe has a non-null `image_path` pointing to an existing file at `public/images/recipes/<slug>.<ext>` and the browse page renders the images

#### Scenario: All 26 recipes have images
- **WHEN** the demo dataset is seeded
- **THEN** exactly 26 image files exist in `public/images/recipes/` and every seeded recipe references one of them

#### Scenario: Image sync is idempotent
- **WHEN** the image sync runs again on an already-synced dataset
- **THEN** no files are duplicated and `image_path` values are unchanged

### Requirement: Orphan test images are removed
The system SHALL remove known orphan test images from the public recipe images directory that do not correspond to any recipe.

#### Scenario: Orphan images deleted
- **WHEN** the seeding/sync flow runs
- **THEN** the files `dfasfds.jpg`, `hello-recipe-test.png`, `hello-world.jpg`, and `traditional-moroccan-goat-tajine-sossi.webp` are removed from `public/images/recipes/` if present

### Requirement: Seeding is non-destructive to user data
The system SHALL preserve all user-created data (recipes, favorites, reviews, accounts) when running the demo data seeding flow.

#### Scenario: User recipes preserved
- **WHEN** a user has created recipes before seeding runs
- **THEN** those recipes remain in the database with their original `user_id`, categories, ingredients, and steps intact

#### Scenario: User accounts preserved
- **WHEN** seeding runs on a database with existing user accounts
- **THEN** all user accounts, sessions, and authentication data remain intact
