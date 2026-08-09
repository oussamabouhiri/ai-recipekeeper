## ADDED Requirements

### Requirement: Favorite toggle on recipe detail page
The system SHALL show a favorite toggle action on the recipe detail page for authenticated users, without changing any other recipe UI behavior.

#### Scenario: Add favorite button
- **WHEN** an authenticated user views a recipe they have not favorited
- **THEN** the recipe detail page shows an add-to-favorites action

#### Scenario: Remove favorite button
- **WHEN** an authenticated user views a recipe they have favorited
- **THEN** the recipe detail page shows a remove-from-favorites action instead

#### Scenario: Hidden recipe visibility unchanged
- **WHEN** a user who is neither the owner nor an admin views a hidden recipe
- **THEN** the recipe detail page still responds with a 404 and no favorite action is offered
