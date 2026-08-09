## ADDED Requirements

### Requirement: Review section on recipe detail page
The system SHALL show a reviews section on the recipe detail page for authenticated users, without changing any other recipe UI behavior.

#### Scenario: Rating summary and review list
- **WHEN** an authenticated user views a recipe they are authorized to see
- **THEN** the recipe detail page shows the recipe's average rating, review count, and the list of its reviews with author names

#### Scenario: Create form when user has not reviewed
- **WHEN** an authenticated user who has not reviewed the recipe views the recipe detail page
- **THEN** the page offers a rating field (1–5) and an optional comment field to create a review

#### Scenario: Edit and delete actions for the user's own review
- **WHEN** an authenticated user who has already reviewed the recipe views the recipe detail page
- **THEN** the page shows their review with edit and delete actions

#### Scenario: Delete action for admins on any review
- **WHEN** an admin views the recipe detail page
- **THEN** the page shows a delete action for every review listed

#### Scenario: Hidden recipe visibility unchanged
- **WHEN** a user who is neither the owner nor an admin views a hidden recipe
- **THEN** the recipe detail page still responds with a 404 and no review section is shown
