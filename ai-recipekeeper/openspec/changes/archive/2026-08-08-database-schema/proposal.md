## Why

The AI Recipe Keeper application requires a robust database schema to support its core features: user management, recipe creation (manual and AI-generated), categorization, ingredient tracking, and user interactions (reviews, favorites). Without a properly designed schema, the application cannot store or retrieve the data needed for these features.

## What Changes

- Create initial database schema based on validated MCD and MLD diagrams
- Define all required tables: utilisateurs, recettes, categories, ingredients, etapes, avis, favoris, generation_ia, and pivot tables
- Implement foreign key constraints and indexes for data integrity and query performance
- Generate Laravel 12 migrations for each table
- Create corresponding Eloquent models with defined relationships

## Capabilities

### New Capabilities

- `database-schema`: Initial database structure for the AI Recipe Keeper application, including all tables, relationships, and constraints defined in the validated MCD/MLD
- `eloquent-models`: Eloquent models representing database entities with their relationships and attributes

### Modified Capabilities

- `user-management`: Extend existing users table with application-specific fields if needed (currently using Laravel's default users table)

## Impact

- New migration files in `database/migrations/`
- New Eloquent models in `app/Models/`
- Database structure will be created when running migrations
- No changes to existing code outside of database and model layers
- All new tables will use Laravel naming conventions and timestamps
