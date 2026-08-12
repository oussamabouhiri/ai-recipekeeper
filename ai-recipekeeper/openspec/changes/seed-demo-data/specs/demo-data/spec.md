## Purpose

Provides a fully populated demo experience for the RecipeKeeper app: seeded categories, ingredients, and 26 recipes with linked images on a fresh database, via an explicit, repeatable seeding flow.

## ADDED Requirements

### Requirement: Demo data can be seeded on an empty database
The system SHALL provide a seeding flow that creates demo data (categories, ingredients, recipes with steps) in the database, and SHALL be safe to run on a database that already contains data.

#### Scenario: Seeding an empty database
- **WHEN** the seeding flow (`db:seed`) is run on a database without recipes
- **THEN** the standard categories (Entrée, Plat principal, Dessert, etc.), the ingredient list, and the 26 demo recipes with their steps are created

#### Scenario: Seeding an already-populated database
- **WHEN** the seeding flow is run a second time on the same database
- **THEN** no duplicate categories, ingredients, or recipes are created and existing user-created recipes are preserved

### Requirement: Demo recipes have linked images
The system SHALL make each demo recipe display its image: the image file SHALL be available at `public/images/recipes/<slug>.<ext>` and the recipe's `image_path` SHALL reference it.

#### Scenario: Image sync after seeding
- **WHEN** demo seeding has created the 26 recipes
- **THEN** each of the 26 recipes has a non-null `image_path` pointing to an existing image file in `public/images/recipes/`, and recipe pages render the image

#### Scenario: All 26 recipes covered
- **WHEN** the demo dataset is seeded
- **THEN** exactly 26 recipes exist, including « Saumon Glacé au Miel et Carottes Rôties », and every recipe has a matching image

#### Scenario: Re-running the sync
- **WHEN** the image sync runs again on an already-synced dataset
- **THEN** no files are duplicated and `image_path` values are unchanged (idempotent)

### Requirement: Seeding is non-destructive and repeatable
The system SHALL make the full seeding flow (seed + image sync) safe to run repeatedly: recipes, categories, and ingredients are matched by name and never duplicated, and existing user-created recipes are preserved.

#### Scenario: Seeder re-run
- **WHEN** the seeder runs a second time on the same database
- **THEN** no duplicate categories, ingredients, or recipes are created and existing user recipes are preserved
