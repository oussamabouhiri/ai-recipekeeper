# category-management Specification

## Purpose

Defines the Category management capability of AI Recipe Keeper: creating, listing, viewing, updating, and deleting categories, including admin-only authorization rules and the cascade behavior when categories are removed.

## Requirements

### Requirement: Category listing
The system SHALL allow listing categories to authenticated users and guests.

#### Scenario: Guest lists categories
- **WHEN** an unauthenticated visitor requests the category list
- **THEN** the system returns all categories

#### Scenario: Authenticated user lists categories
- **WHEN** an authenticated user requests the category list
- **THEN** the system returns all categories

### Requirement: Category viewing
The system SHALL allow viewing a single category by authenticated users and guests.

#### Scenario: Guest views a category
- **WHEN** an unauthenticated visitor views a category
- **THEN** the system returns the category

#### Scenario: Authenticated user views a category
- **WHEN** an authenticated user views a category
- **THEN** the system returns the category

### Requirement: Category creation
The system SHALL allow admin users to create categories and SHALL reject non-admin creation attempts.

#### Scenario: Admin creates a category
- **WHEN** an admin submits a valid category payload
- **THEN** the system creates the category with the provided name and description

#### Scenario: Non-admin creation rejected
- **WHEN** a user who is not an admin submits a category
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest creation rejected
- **WHEN** an unauthenticated visitor submits a category
- **THEN** the system rejects the request with a 401 response

### Requirement: Category update
The system SHALL allow admin users to update categories and SHALL reject non-admin update attempts.

#### Scenario: Admin updates a category
- **WHEN** an admin submits a valid update to a category
- **THEN** the system updates the category with the provided values

#### Scenario: Non-admin update rejected
- **WHEN** a user who is not an admin submits an update to a category
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest update rejected
- **WHEN** an unauthenticated visitor submits an update to a category
- **THEN** the system rejects the request with a 401 response

### Requirement: Category deletion
The system SHALL allow admin users to delete categories and SHALL reject non-admin deletion attempts. Deleting a category SHALL remove its associations with recipes through the pivot table cascade behavior.

#### Scenario: Admin deletes a category
- **WHEN** an admin deletes a category
- **THEN** the system deletes the category and removes all recipe associations through cascade

#### Scenario: Admin deletes a category attached to recipes
- **WHEN** an admin deletes a category that is associated with one or more recipes
- **THEN** the system deletes the category and the pivot associations are removed by cascade
- **AND** the recipes themselves are not deleted or modified

#### Scenario: Non-admin deletion rejected
- **WHEN** a user who is not an admin attempts to delete a category
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest deletion rejected
- **WHEN** an unauthenticated visitor attempts to delete a category
- **THEN** the system rejects the request with a 401 response

### Requirement: Category validation
The system SHALL validate category payloads and reject invalid ones with a 422 response.

#### Scenario: Missing name rejected
- **WHEN** a category payload has no name
- **THEN** the system responds with a 422 validation error

#### Scenario: Duplicate name rejected
- **WHEN** a category payload has a name that already exists
- **THEN** the system responds with a 422 validation error

#### Scenario: Name too long rejected
- **WHEN** a category payload has a name exceeding 255 characters
- **THEN** the system responds with a 422 validation error

### Requirement: Category API routes
The system SHALL expose category CRUD through a REST API.

#### Scenario: List endpoint
- **WHEN** a client requests `GET /api/categories`
- **THEN** the system returns a collection of categories

#### Scenario: Show endpoint
- **WHEN** a client requests `GET /api/categories/{category}`
- **THEN** the system returns the category

#### Scenario: Create endpoint
- **WHEN** an admin client requests `POST /api/categories` with a valid payload
- **THEN** the system creates the category and returns it with a 201 response

#### Scenario: Update endpoint
- **WHEN** an admin client requests `PUT /api/categories/{category}` with a valid payload
- **THEN** the system updates the category and returns it

#### Scenario: Delete endpoint
- **WHEN** an admin client requests `DELETE /api/categories/{category}`
- **THEN** the system deletes the category and returns a 204 response

### Requirement: Category Blade views
The system SHALL provide admin Blade views for managing categories.

#### Scenario: Admin category index page
- **WHEN** an admin visits the category management page
- **THEN** the system displays a list of all categories with edit and delete actions

#### Scenario: Admin category create page
- **WHEN** an admin visits the category creation page
- **THEN** the system displays a form with name and description fields

#### Scenario: Admin category edit page
- **WHEN** an admin visits the category edit page
- **THEN** the system displays a form pre-filled with the category's current values

#### Scenario: Non-admin cannot access category management
- **WHEN** a user who is not an admin visits the category management pages
- **THEN** the system redirects or returns 403
