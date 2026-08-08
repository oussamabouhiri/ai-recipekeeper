## Purpose

Extend user management capabilities to support recipe-related relationships and operations.

## ADDED Requirements

### Requirement: User-Recipe relationship
The system SHALL allow users to own recipes.

#### Scenario: Recipe ownership
- **WHEN** a user creates a recipe
- **THEN** the recipe is associated with the user through user_id foreign key

### Requirement: User-Review relationship
The system SHALL allow users to create reviews.

#### Scenario: Review creation
- **WHEN** a user submits a review
- **THEN** the review is associated with the user through user_id foreign key

### Requirement: User-Favorite relationship
The system SHALL allow users to favorite recipes.

#### Scenario: Favorite creation
- **WHEN** a user favorites a recipe
- **THEN** the favorite is associated with the user through user_id foreign key

### Requirement: User-AI generation relationship
The system SHALL allow users to have AI generation history.

#### Scenario: Generation tracking
- **WHEN** a user generates an AI recipe
- **THEN** the generation is associated with the user through user_id foreign key
