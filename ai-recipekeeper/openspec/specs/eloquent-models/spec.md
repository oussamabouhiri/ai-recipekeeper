# eloquent-models Specification

## Purpose

Define Eloquent models representing database entities with their relationships and attributes for the AI Recipe Keeper application.

## Requirements

### Requirement: User model

The system SHALL provide a User model for user entity management.

#### Scenario: User relationships

- **WHEN** a User is loaded
- **THEN** the model defines relationships to recipes, reviews, favorites, and AI generations

### Requirement: Recipe model

The system SHALL provide a Recipe model for recipe entity management.

#### Scenario: Recipe relationships

- **WHEN** a Recipe is loaded
- **THEN** the model defines relationships to user, categories, ingredients, steps, reviews, and favorites

### Requirement: Category model

The system SHALL provide a Category model for category entity management.

#### Scenario: Category relationships

- **WHEN** a Category is loaded
- **THEN** the model defines a many-to-many relationship with recipes

### Requirement: Ingredient model

The system SHALL provide an Ingredient model for ingredient entity management.

#### Scenario: Ingredient relationships

- **WHEN** an Ingredient is loaded
- **THEN** the model defines a many-to-many relationship with recipes

### Requirement: Step model

The system SHALL provide a Step model for recipe step entity management.

#### Scenario: Step relationships

- **WHEN** a Step is loaded
- **THEN** the model defines a belongs-to relationship with recipe

### Requirement: Review model

The system SHALL provide a Review model for review entity management.

#### Scenario: Review relationships

- **WHEN** a Review is loaded
- **THEN** the model defines belongs-to relationships with recipe and user

### Requirement: Favorite model

The system SHALL provide a Favorite model for favorite entity management.

#### Scenario: Favorite relationships

- **WHEN** a Favorite is loaded
- **THEN** the model defines belongs-to relationships with user and recipe

### Requirement: AI generation model

The system SHALL provide a GenerationIa model for AI generation entity management.

#### Scenario: Generation relationships

- **WHEN** a GenerationIa is loaded
- **THEN** the model defines a belongs-to relationship with user
