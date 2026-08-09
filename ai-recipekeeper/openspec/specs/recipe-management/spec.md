# recipe-management Specification

## Purpose

Defines the Recipe CRUD API of AI Recipe Keeper: creating, browsing, viewing, updating, and deleting recipes, including the published/hidden visibility model and the owner-or-admin authorization rules.

## Requirements

### Requirement: Recipe creation
The system SHALL allow authenticated users to create recipes and SHALL reject unauthenticated creation attempts.

#### Scenario: Authenticated user creates a recipe
- **WHEN** an authenticated user submits a valid recipe
- **THEN** the system creates the recipe with a `statut` defaulting to `published`
- **AND** creates the provided steps as `Etape` records through the existing `etapes` relationship
- **AND** associates the provided ingredients (with pivot `quantity` and `unit`) and categories through the existing pivot relationships
- **AND** records the authenticated user as the recipe owner

#### Scenario: Guest creation rejected
- **WHEN** an unauthenticated visitor submits a recipe
- **THEN** the system rejects the request with a 401 response

#### Scenario: Owner attribution
- **WHEN** a recipe is created
- **THEN** the `user_id` of the recipe is the creator's user id and cannot be supplied by the client

### Requirement: Recipe listing
The system SHALL list recipes according to visibility: published recipes are browsable by anyone, hidden recipes only by their owner, and all recipes by admins.

#### Scenario: Guest browses published recipes
- **WHEN** an unauthenticated visitor lists recipes
- **THEN** the system returns only recipes with `statut` = `published`

#### Scenario: Guest does not see hidden recipes
- **WHEN** an unauthenticated visitor lists recipes
- **THEN** the system does not return recipes with `statut` = `hidden`

#### Scenario: Owner browses own hidden recipes
- **WHEN** an authenticated user lists recipes
- **THEN** the system returns published recipes plus the user's own hidden recipes

#### Scenario: User does not see others' hidden recipes
- **WHEN** an authenticated user lists recipes
- **THEN** the system does not return hidden recipes owned by other users

#### Scenario: Admin browses all recipes
- **WHEN** an admin lists recipes
- **THEN** the system returns all recipes regardless of `statut`

### Requirement: Recipe viewing
The system SHALL allow viewing a single published recipe by anyone, and a hidden recipe only by its owner or an admin.

#### Scenario: Guest views published recipe
- **WHEN** an unauthenticated visitor views a published recipe
- **THEN** the system returns the recipe

#### Scenario: Guest views hidden recipe
- **WHEN** an unauthenticated visitor views a hidden recipe
- **THEN** the system responds as if the recipe does not exist (404)

#### Scenario: Owner views own hidden recipe
- **WHEN** the owner views their own hidden recipe
- **THEN** the system returns the recipe

#### Scenario: Admin views hidden recipe
- **WHEN** an admin views a hidden recipe
- **THEN** the system returns the recipe

### Requirement: Recipe update
The system SHALL allow the recipe owner or an admin to update a recipe, and SHALL reject other users.

#### Scenario: Owner updates own recipe
- **WHEN** the owner submits a valid update to their own recipe
- **THEN** the system updates the recipe and replaces its `Etape` records and pivot associations (ingredients with `quantity` and `unit`, and categories)

#### Scenario: Admin updates any recipe
- **WHEN** an admin submits a valid update to any recipe
- **THEN** the system updates the recipe

#### Scenario: Non-owner update rejected
- **WHEN** a user who is neither the owner nor an admin submits an update
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest update rejected
- **WHEN** an unauthenticated visitor submits an update
- **THEN** the system rejects the request with a 401 response

### Requirement: Recipe deletion
The system SHALL allow the recipe owner or an admin to delete a recipe, SHALL reject other users, and SHALL cascade to dependent data.

#### Scenario: Owner deletes own recipe
- **WHEN** the owner deletes their own recipe
- **THEN** the system deletes the recipe and its `Etape` records and pivot associations

#### Scenario: Admin deletes any recipe
- **WHEN** an admin deletes any recipe
- **THEN** the system deletes the recipe and its `Etape` records and pivot associations

#### Scenario: Non-owner deletion rejected
- **WHEN** a user who is neither the owner nor an admin attempts to delete a recipe
- **THEN** the system rejects the request with a 403 response

### Requirement: Recipe visibility field
The system SHALL allow the recipe owner to set a `statut` of `published` or `hidden` on their own recipe at creation or update, defaulting to `published`.

#### Scenario: Default status
- **WHEN** a recipe is created without specifying a status
- **THEN** the recipe `statut` is `published`

#### Scenario: Owner chooses visibility
- **WHEN** the owner creates or updates their own recipe with a `statut` of `published` or `hidden`
- **THEN** the recipe `statut` is set to the provided value
- **AND** no admin action is required to publish or hide the recipe

### Requirement: Recipe relationships
The system SHALL expose the recipe's relationships to steps, ingredients, categories, and owner.

#### Scenario: Recipe steps
- **WHEN** a recipe is returned
- **THEN** the response includes its steps as `Etape` records ordered by step number

#### Scenario: Recipe ingredients with quantities
- **WHEN** a recipe is returned
- **THEN** the response includes its ingredients with their pivot `quantity` and `unit`

#### Scenario: Recipe categories
- **WHEN** a recipe is returned
- **THEN** the response includes its categories

#### Scenario: Recipe owner
- **WHEN** a recipe is returned
- **THEN** the response includes the owning user

### Requirement: Recipe validation
The system SHALL validate recipe payloads and reject invalid ones with a 422 response.

#### Scenario: Missing title rejected
- **WHEN** a recipe payload has no title
- **THEN** the system responds with a 422 validation error

#### Scenario: Invalid status rejected
- **WHEN** a recipe payload has a `statut` other than `published` or `hidden`
- **THEN** the system responds with a 422 validation error

#### Scenario: Unknown ingredient rejected
- **WHEN** a recipe payload references an ingredient id that does not exist
- **THEN** the system responds with a 422 validation error

#### Scenario: Invalid step rejected
- **WHEN** a recipe payload has a step entry (an `Etape`) without a positive step number or an instruction
- **THEN** the system responds with a 422 validation error

#### Scenario: Invalid numeric fields rejected
- **WHEN** a recipe payload has negative preparation time, cooking time, or a servings value below 1
- **THEN** the system responds with a 422 validation error

### Requirement: Recipe API routes
The system SHALL expose recipe CRUD through a REST API under `/api/recipes` AND through web routes under `/recipes` using a dedicated web controller.

#### Scenario: List endpoint
- **WHEN** a client requests `GET /api/recipes`
- **THEN** the system returns a collection of recipes per the listing visibility rules

#### Scenario: Create endpoint
- **WHEN** an authenticated client requests `POST /api/recipes` with a valid payload
- **THEN** the system creates the recipe and returns it with a 201 response

#### Scenario: Show endpoint
- **WHEN** a client requests `GET /api/recipes/{recipe}`
- **THEN** the system returns the recipe per the viewing visibility rules

#### Scenario: Update endpoint
- **WHEN** an authorized client requests `PUT /api/recipes/{recipe}` with a valid payload
- **THEN** the system updates the recipe and returns it

#### Scenario: Delete endpoint
- **WHEN** an authorized client requests `DELETE /api/recipes/{recipe}`
- **THEN** the system deletes the recipe and returns a success response

#### Scenario: Web list route
- **WHEN** an authenticated user requests `GET /recipes`
- **THEN** the system renders a Blade page listing recipes per the listing visibility rules

#### Scenario: Web create route
- **WHEN** an authenticated user requests `GET /recipes/create`
- **THEN** the system renders a Blade form for creating a recipe

#### Scenario: Web store route
- **WHEN** an authenticated user submits `POST /recipes` with a valid payload
- **THEN** the system creates the recipe and redirects to the recipe detail page

#### Scenario: Web show route
- **WHEN** an authenticated user requests `GET /recipes/{recipe}`
- **THEN** the system renders a Blade page showing the recipe per the viewing visibility rules

#### Scenario: Web edit route
- **WHEN** the owner or admin requests `GET /recipes/{recipe}/edit`
- **THEN** the system renders a Blade form pre-filled with the recipe data

#### Scenario: Web update route
- **WHEN** the owner or admin submits `PUT /recipes/{recipe}` with a valid payload
- **THEN** the system updates the recipe and redirects to the recipe detail page

#### Scenario: Web destroy route
- **WHEN** the owner or admin submits `DELETE /recipes/{recipe}`
- **THEN** the system deletes the recipe and redirects to the recipe list
