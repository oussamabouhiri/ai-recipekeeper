## Purpose

Lets authenticated users bookmark recipes they like, view their bookmarked recipes on a dedicated "My Favorites" page, and remove bookmarks, with duplicate and cascade safeguards backed by the existing favoris table.

## ADDED Requirements

### Requirement: Favorite creation
The system SHALL allow authenticated users to favorite a recipe they are authorized to view, and SHALL reject unauthenticated attempts.

#### Scenario: Authenticated user favorites a recipe
- **WHEN** an authenticated user submits a favorite for a visible recipe
- **THEN** the system creates a Favori record with the authenticated user's id and the recipe id
- **AND** the recipe can be favorited only once by the same user

#### Scenario: Guest favorite rejected
- **WHEN** an unauthenticated visitor submits a favorite
- **THEN** the system rejects the request with a 401 response

#### Scenario: Owner attribution
- **WHEN** a favorite is created
- **THEN** the `user_id` of the favorite is the authenticated user's id and cannot be supplied by the client

### Requirement: Favorite duplication prevention
The system SHALL prevent a user from favoriting the same recipe more than once.

#### Scenario: Duplicate favorite rejected
- **WHEN** a user submits a favorite for a recipe they already favorited
- **THEN** the system rejects the request with a 422 validation error
- **AND** the database unique constraint on `(user_id, recette_id)` prevents duplicate rows as a backstop

### Requirement: Favorite visibility rule
The system SHALL only allow favoriting recipes the current user is authorized to view.

#### Scenario: User favorites own hidden recipe
- **WHEN** the owner of a hidden recipe favorites their own recipe
- **THEN** the system creates the favorite

#### Scenario: Hidden recipe of another user rejected
- **WHEN** a user submits a favorite for a hidden recipe they do not own
- **THEN** the system rejects the request as if the recipe does not exist

#### Scenario: Admin favorites any recipe
- **WHEN** an admin submits a favorite for any recipe
- **THEN** the system creates the favorite

### Requirement: Favorite listing
The system SHALL list the authenticated user's own favorites with their recipes, and SHALL NOT reveal other users' favorites.

#### Scenario: User lists own favorites
- **WHEN** an authenticated user requests the favorite list
- **THEN** the system returns only the favorites created by that user
- **AND** each favorite includes its favorited recipe

#### Scenario: Guest listing rejected
- **WHEN** an unauthenticated visitor requests the favorite list
- **THEN** the system rejects the request with a 401 response

#### Scenario: No favorites yet
- **WHEN** an authenticated user with no favorites requests the favorite list
- **THEN** the system returns an empty collection

### Requirement: Favorite removal
The system SHALL allow a user to remove their own favorite, allow an admin to remove any favorite, and SHALL reject other users.

#### Scenario: Owner removes own favorite
- **WHEN** the owner of a favorite removes it
- **THEN** the system deletes the favorite

#### Scenario: Admin removes any favorite
- **WHEN** an admin removes any favorite
- **THEN** the system deletes the favorite

#### Scenario: Non-owner removal rejected
- **WHEN** a user who is not the favorite owner and not an admin attempts to remove a favorite
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest removal rejected
- **WHEN** an unauthenticated visitor attempts to remove a favorite
- **THEN** the system rejects the request with a 401 response

### Requirement: Favorite cascade behavior
The system SHALL remove favorites automatically when their parent recipe or user is deleted.

#### Scenario: Recipe deletion removes favorites
- **WHEN** a recipe is deleted
- **THEN** all favorites referencing the recipe are deleted automatically

#### Scenario: User deletion removes favorites
- **WHEN** a user account is deleted
- **THEN** all favorites created by that user are deleted automatically

### Requirement: Favorite validation
The system SHALL validate favorite payloads and reject invalid ones with a 422 response.

#### Scenario: Missing recipe rejected
- **WHEN** a favorite payload has no `recette_id`
- **THEN** the system responds with a 422 validation error

#### Scenario: Unknown recipe rejected
- **WHEN** a favorite payload references a recipe id that does not exist
- **THEN** the system responds with a 422 validation error

### Requirement: Favorite API routes
The system SHALL expose favorite management through a REST API under `/api/favorites`.

#### Scenario: List endpoint
- **WHEN** an authenticated client requests `GET /api/favorites`
- **THEN** the system returns a collection of the client user's favorites with their recipes

#### Scenario: Create endpoint
- **WHEN** an authenticated client requests `POST /api/favorites` with a valid `recette_id`
- **THEN** the system creates the favorite and returns it with a 201 response

#### Scenario: Delete endpoint
- **WHEN** an authorized client requests `DELETE /api/favorites/{favori}`
- **THEN** the system deletes the favorite and returns a 204 response

### Requirement: Favorite Blade UI
The system SHALL provide a Blade interface for authenticated users to browse and manage their favorites in the browser.

#### Scenario: My Favorites page
- **WHEN** an authenticated user navigates to `GET /favorites`
- **THEN** the system renders a page listing the user's favorited recipes with view and remove actions

#### Scenario: Favorites page empty state
- **WHEN** an authenticated user with no favorites navigates to `GET /favorites`
- **THEN** the system displays an empty state message

#### Scenario: Add favorite from recipe detail
- **WHEN** an authenticated user submits the favorite action on a recipe they have not favorited
- **THEN** the system creates the favorite and returns the user to the recipe detail page

#### Scenario: Remove favorite from recipe detail
- **WHEN** an authenticated user submits the favorite action on a recipe they have favorited
- **THEN** the system deletes the favorite and returns the user to the recipe detail page

#### Scenario: Remove favorite from My Favorites page
- **WHEN** an authenticated user submits the remove action on the My Favorites page
- **THEN** the system deletes the favorite and returns the user to the My Favorites page

#### Scenario: Favorite toggle state
- **WHEN** an authenticated user views a recipe detail page
- **THEN** the page indicates whether the recipe is favorited and offers an add or remove action accordingly

#### Scenario: Guest web access rejected
- **WHEN** a guest visits any favorites web page
- **THEN** the system redirects the guest to the login page

### Requirement: Favorite web routes
The system SHALL expose favorite management through web routes under `/favorites`.

#### Scenario: Web list route
- **WHEN** an authenticated user requests `GET /favorites`
- **THEN** the system renders the My Favorites page

#### Scenario: Web store route
- **WHEN** an authenticated user submits `POST /favorites` with a valid `recette_id`
- **THEN** the system creates the favorite and redirects back with a success message

#### Scenario: Web destroy route
- **WHEN** an authorized user submits `DELETE /favorites/{favori}`
- **THEN** the system deletes the favorite and redirects back with a success message
