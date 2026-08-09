# reviews-ratings Specification

## Purpose

Lets users rate and review recipes with a 1–5 star rating and an optional comment, view a recipe's average rating and review count, and manage their own reviews, backed by the existing avis table.

## Requirements

### Requirement: Review creation
The system SHALL allow authenticated users to review a recipe they are authorized to view, and SHALL reject unauthenticated attempts. A user SHALL be allowed to review their own recipe.

#### Scenario: Authenticated user reviews a recipe
- **WHEN** an authenticated user submits a review for a visible recipe with a valid rating and an optional comment
- **THEN** the system creates an Avis record with the authenticated user's id, the recipe id, the rating, and the comment

#### Scenario: Guest review rejected
- **WHEN** an unauthenticated visitor submits a review
- **THEN** the system rejects the request with a 401 response

#### Scenario: Owner attribution
- **WHEN** a review is created
- **THEN** the `user_id` of the review is the authenticated user's id and cannot be supplied by the client

#### Scenario: User reviews own recipe
- **WHEN** the owner of a recipe submits a review for their own recipe
- **THEN** the system creates the review

### Requirement: Review duplication prevention
The system SHALL prevent a user from reviewing the same recipe more than once.

#### Scenario: Duplicate review rejected
- **WHEN** a user submits a review for a recipe they already reviewed
- **THEN** the system rejects the request with a 422 validation error

#### Scenario: Same recipe reviewable by different users
- **WHEN** two different users review the same recipe
- **THEN** the system creates both reviews

### Requirement: Review visibility rule
The system SHALL only allow reviewing recipes the current user is authorized to view.

#### Scenario: User reviews own hidden recipe
- **WHEN** the owner of a hidden recipe reviews their own recipe
- **THEN** the system creates the review

#### Scenario: Hidden recipe of another user rejected
- **WHEN** a user submits a review for a hidden recipe they do not own
- **THEN** the system rejects the request as if the recipe does not exist

#### Scenario: Admin reviews any recipe
- **WHEN** an admin submits a review for any recipe
- **THEN** the system creates the review

### Requirement: Review listing
The system SHALL list the reviews of a recipe the current user is authorized to view, ordered with the most recent first, and SHALL include each review's author.

#### Scenario: Reviews of a visible recipe are listed
- **WHEN** an authenticated user or guest requests the review list of a visible recipe
- **THEN** the system returns the recipe's reviews with each review's author name

#### Scenario: Hidden recipe of another user not listed
- **WHEN** a user requests the review list of a hidden recipe they do not own
- **THEN** the system responds as if the recipe does not exist

#### Scenario: Recipe with no reviews
- **WHEN** the review list is requested for a visible recipe that has no reviews
- **THEN** the system returns an empty collection

### Requirement: Review aggregates
The system SHALL expose the average rating and the review count for a recipe whenever its reviews are listed.

#### Scenario: Average and count with reviews
- **WHEN** the review list is requested for a recipe that has reviews
- **THEN** the response includes the recipe's average rating and its review count

#### Scenario: Average and count without reviews
- **WHEN** the review list is requested for a recipe that has no reviews
- **THEN** the response includes a null average rating and a review count of zero

### Requirement: Review update
The system SHALL allow the review owner or an admin to update a review, and SHALL reject other users.

#### Scenario: Owner updates own review
- **WHEN** the owner submits a valid update to their own review
- **THEN** the system updates the review's rating and comment

#### Scenario: Admin updates any review
- **WHEN** an admin submits a valid update to any review
- **THEN** the system updates the review

#### Scenario: Non-owner update rejected
- **WHEN** a user who is neither the review owner nor an admin submits an update
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest update rejected
- **WHEN** an unauthenticated visitor submits an update
- **THEN** the system rejects the request with a 401 response

### Requirement: Review deletion
The system SHALL allow the review owner or an admin to delete a review, and SHALL reject other users.

#### Scenario: Owner deletes own review
- **WHEN** the owner of a review deletes it
- **THEN** the system deletes the review

#### Scenario: Admin deletes any review
- **WHEN** an admin deletes any review
- **THEN** the system deletes the review

#### Scenario: Non-owner deletion rejected
- **WHEN** a user who is neither the review owner nor an admin attempts to delete a review
- **THEN** the system rejects the request with a 403 response

#### Scenario: Guest deletion rejected
- **WHEN** an unauthenticated visitor attempts to delete a review
- **THEN** the system rejects the request with a 401 response

### Requirement: Review cascade behavior
The system SHALL remove reviews automatically when their parent recipe or user is deleted.

#### Scenario: Recipe deletion removes reviews
- **WHEN** a recipe is deleted
- **THEN** all reviews referencing the recipe are deleted automatically

#### Scenario: User deletion removes reviews
- **WHEN** a user account is deleted
- **THEN** all reviews created by that user are deleted automatically

### Requirement: Review validation
The system SHALL validate review payloads and reject invalid ones with a 422 response.

#### Scenario: Missing rating rejected
- **WHEN** a review payload has no rating
- **THEN** the system responds with a 422 validation error

#### Scenario: Rating out of range rejected
- **WHEN** a review payload has a rating outside the 1–5 range or not an integer
- **THEN** the system responds with a 422 validation error

#### Scenario: Comment too long rejected
- **WHEN** a review payload has a comment longer than 1000 characters
- **THEN** the system responds with a 422 validation error

#### Scenario: Empty comment allowed
- **WHEN** a review payload has no comment
- **THEN** the system creates the review with a null comment

### Requirement: Review API routes
The system SHALL expose review management through a REST API under `/api/recipes/{recipe}/reviews` and `/api/reviews/{avis}`.

#### Scenario: List endpoint
- **WHEN** a client requests `GET /api/recipes/{recipe}/reviews` for a visible recipe
- **THEN** the system returns the recipe's reviews with the rating aggregate

#### Scenario: Create endpoint
- **WHEN** an authenticated client requests `POST /api/recipes/{recipe}/reviews` with a valid payload
- **THEN** the system creates the review and returns it with a 201 response

#### Scenario: Update endpoint
- **WHEN** an authorized client requests `PUT /api/reviews/{avis}` with a valid payload
- **THEN** the system updates the review and returns it

#### Scenario: Delete endpoint
- **WHEN** an authorized client requests `DELETE /api/reviews/{avis}`
- **THEN** the system deletes the review and returns a 204 response

### Requirement: Review Blade UI
The system SHALL provide a reviews section on the recipe detail page for authenticated users.

#### Scenario: Reviews section on recipe detail
- **WHEN** an authenticated user views a recipe they are authorized to see
- **THEN** the page displays the recipe's average rating, review count, and its list of reviews with author names

#### Scenario: Review creation form
- **WHEN** an authenticated user who has not reviewed the recipe views the recipe detail page
- **THEN** the page offers a form with a 1–5 rating field and an optional comment field

#### Scenario: Successful review creation
- **WHEN** the user submits a valid review form
- **THEN** the system creates the review and returns the user to the recipe detail page with a success message

#### Scenario: Edit and delete own review
- **WHEN** an authenticated user who has already reviewed the recipe views the recipe detail page
- **THEN** the page shows their review with edit and delete actions instead of a creation form

#### Scenario: Successful review update
- **WHEN** the owner submits a valid update to their review
- **THEN** the system updates the review and returns the user to the recipe detail page with a success message

#### Scenario: Admin moderates any review
- **WHEN** an admin views the recipe detail page
- **THEN** the page shows a delete action for every review, including other users' reviews

#### Scenario: Validation errors displayed
- **WHEN** the user submits an invalid review form
- **THEN** the system redisplays the recipe detail page with validation errors

#### Scenario: Guest web access rejected
- **WHEN** a guest submits any review form
- **THEN** the system redirects the guest to the login page

### Requirement: Review web routes
The system SHALL expose review management through web routes under `/recipes/{recipe}/reviews` and `/reviews/{avis}`.

#### Scenario: Web store route
- **WHEN** an authenticated user submits `POST /recipes/{recipe}/reviews` with a valid payload
- **THEN** the system creates the review and redirects back with a success message

#### Scenario: Web update route
- **WHEN** an authorized user submits `PUT /reviews/{avis}` with a valid payload
- **THEN** the system updates the review and redirects back with a success message

#### Scenario: Web destroy route
- **WHEN** an authorized user submits `DELETE /reviews/{avis}`
- **THEN** the system deletes the review and redirects back with a success message
