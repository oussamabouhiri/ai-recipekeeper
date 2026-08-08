# database-schema Specification

## Purpose

Define the initial database schema for the AI Recipe Keeper application, including all required tables, relationships, and constraints based on the validated MCD and MLD diagrams.

## Requirements

### Requirement: Users table
The system SHALL maintain a users table for authentication and user management.

#### Scenario: User creation
- **WHEN** a new user registers
- **THEN** the system creates a record with id, name, email, password, email_verified_at, remember_token, created_at, and updated_at fields

### Requirement: Recipes table
The system SHALL maintain a recipes table to store recipe information.

#### Scenario: Recipe storage
- **WHEN** a recipe is created
- **THEN** the system stores title, description, instructions, prep_time, cook_time, servings, difficulty, image_path, user_id, is_ai_generated, and timestamps

### Requirement: Categories table
The system SHALL maintain a categories table for recipe categorization.

#### Scenario: Category creation
- **WHEN** a category is created
- **THEN** the system stores name, description, and timestamps

### Requirement: Ingredients table
The system SHALL maintain an ingredients table for recipe ingredients.

#### Scenario: Ingredient storage
- **WHEN** an ingredient is created
- **THEN** the system stores name, unit, and timestamps

### Requirement: Steps table
The system SHALL maintain a steps table for recipe instructions.

#### Scenario: Step creation
- **WHEN** a recipe step is created
- **THEN** the system stores recipe_id, step_number, instruction, and timestamps

### Requirement: Reviews table
The system SHALL maintain a reviews table for user ratings and comments.

#### Scenario: Review submission
- **WHEN** a user submits a review
- **THEN** the system stores recipe_id, user_id, rating, comment, and timestamps

### Requirement: Favorites table
The system SHALL maintain a favorites table for user recipe bookmarks.

#### Scenario: Favorite creation
- **WHEN** a user favorites a recipe
- **THEN** the system stores user_id, recipe_id, and timestamps

### Requirement: AI generation history table
The system SHALL maintain a generation_ia table to track AI recipe generations.

#### Scenario: Generation logging
- **WHEN** an AI recipe is generated
- **THEN** the system stores user_id, prompt, response, model_used, tokens_used, and timestamps

### Requirement: Recipe-Ingredient pivot table
The system SHALL maintain a recette_ingredient pivot table for recipe-ingredient relationships.

#### Scenario: Ingredient association
- **WHEN** an ingredient is added to a recipe
- **THEN** the system stores recipe_id, ingredient_id, quantity, and unit in the pivot table

### Requirement: Recipe-Category pivot table
The system SHALL maintain a recette_categorie pivot table for recipe-category relationships.

#### Scenario: Category association
- **WHEN** a category is assigned to a recipe
- **THEN** the system stores recipe_id and category_id in the pivot table

### Requirement: Foreign key constraints
The system SHALL enforce referential integrity through foreign key constraints.

#### Scenario: Cascade delete
- **WHEN** a parent record is deleted
- **THEN** all related child records are automatically deleted

### Requirement: Database indexes
The system SHALL create appropriate indexes for query performance.

#### Scenario: Indexed columns
- **WHEN** queries filter by frequently accessed columns
- **THEN** the system uses indexes to optimize query performance
