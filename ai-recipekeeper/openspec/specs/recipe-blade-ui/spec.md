# recipe-blade-ui Specification

## Purpose

Provides a Blade-based web interface for authenticated users to create, browse, view, edit, and delete recipes through the browser, complementing the existing API.

## Requirements

### Requirement: Authenticated app layout
The system SHALL provide a shared authenticated layout (`layouts/app.blade.php`) with a responsive navbar, the authenticated user's name, and navigation links to the dashboard and recipe list.

#### Scenario: Layout renders for authenticated user
- **WHEN** an authenticated user navigates to any recipe page
- **THEN** the system renders the page within a shared layout that includes a navbar with the application name, the user's name, and a logout button

#### Scenario: Layout excludes guest users
- **WHEN** a guest visits any recipe page
- **THEN** the system redirects the guest to the login page

### Requirement: Recipe list page
The system SHALL provide a Blade page at `/recipes` where authenticated users see a list of recipes they are authorized to view.

#### Scenario: User sees own recipes
- **WHEN** an authenticated user navigates to the recipe list
- **THEN** the system displays all published recipes and the user's own hidden recipes, showing each recipe's title, status (published/hidden), and action buttons (view, edit, delete)

#### Scenario: Admin sees all recipes
- **WHEN** an admin navigates to the recipe list
- **THEN** the system displays all recipes regardless of status

#### Scenario: Empty list state
- **WHEN** the authenticated user has no recipes and no published recipes exist
- **THEN** the system displays an empty state message with a link to create a recipe

### Requirement: Recipe detail page
The system SHALL provide a Blade page at `/recipes/{recipe}` showing the full details of a recipe.

#### Scenario: Display published recipe
- **WHEN** an authenticated user views a published recipe
- **THEN** the system displays the title, description, preparation time, cooking time, servings, difficulty, status, categories, ingredients (with quantity and unit), and ordered steps

#### Scenario: Owner views hidden recipe
- **WHEN** the owner views their own hidden recipe
- **THEN** the system displays the recipe with all details

#### Scenario: Non-owner cannot view hidden recipe
- **WHEN** a user who is neither the owner nor an admin views a hidden recipe
- **THEN** the system responds with a 404 page

#### Scenario: Actions for owner
- **WHEN** the owner views their own recipe
- **THEN** the system shows Edit and Delete action buttons

#### Scenario: Actions for admin
- **WHEN** an admin views any recipe
- **THEN** the system shows Edit and Delete action buttons

#### Scenario: Actions for non-owner
- **WHEN** a user who is neither the owner nor an admin views a recipe
- **THEN** the system does not show Edit or Delete buttons

### Requirement: Create recipe form
The system SHALL provide a Blade form at `/recipes/create` for authenticated users to create a new recipe.

#### Scenario: Form renders with all fields
- **WHEN** an authenticated user navigates to the create recipe page
- **THEN** the system displays a form with fields for title, description, preparation time, cooking time, servings, difficulty, status (published/hidden), categories (multi-select), ingredients (dynamic entries with ingredient dropdown, quantity, and unit), and steps (dynamic entries with step number and instruction)

#### Scenario: Successful creation
- **WHEN** the user submits a valid recipe form
- **THEN** the system creates the recipe with the authenticated user as owner, redirects to the recipe detail page, and displays a success message

#### Scenario: Validation errors displayed
- **WHEN** the user submits an invalid recipe form (e.g., missing title)
- **THEN** the system redisplays the form with validation error messages next to the invalid fields

#### Scenario: User cannot set user_id
- **WHEN** the user submits the create recipe form
- **THEN** the system ignores any user_id value and sets the owner to the authenticated user

### Requirement: Edit recipe form
The system SHALL provide a Blade form at `/recipes/{recipe}/edit` for the recipe owner or an admin to update a recipe.

#### Scenario: Form pre-fills existing data
- **WHEN** the owner navigates to the edit page for their recipe
- **THEN** the system displays the form pre-filled with the recipe's current title, description, preparation time, cooking time, servings, difficulty, status, categories, ingredients, and steps

#### Scenario: Successful update
- **WHEN** the owner submits a valid update
- **THEN** the system updates the recipe, redirects to the recipe detail page, and displays a success message

#### Scenario: Validation errors on update
- **WHEN** the owner submits an invalid update
- **THEN** the system redisplays the form with validation error messages

#### Scenario: Non-owner cannot edit
- **WHEN** a user who is neither the owner nor an admin navigates to the edit page
- **THEN** the system responds with a 403 forbidden response

### Requirement: Delete recipe action
The system SHALL provide a delete action on the recipe list and detail pages, restricted to the recipe owner or an admin.

#### Scenario: Owner deletes recipe
- **WHEN** the owner clicks the delete action for their recipe and confirms
- **THEN** the system deletes the recipe and redirects to the recipe list with a success message

#### Scenario: Delete uses correct HTTP method
- **WHEN** a delete action is triggered
- **THEN** the system sends a DELETE request with CSRF token protection

#### Scenario: Non-owner cannot delete
- **WHEN** a user who is neither the owner nor an admin attempts to delete a recipe
- **THEN** the system rejects the request with a 403 forbidden response

### Requirement: Recipe web routes
The system SHALL expose recipe CRUD through web routes under `/recipes` using a dedicated web controller.

#### Scenario: Web routes are protected
- **WHEN** a guest accesses any `/recipes` web route
- **THEN** the system redirects the guest to the login page

#### Scenario: Web routes use existing validation
- **WHEN** a user submits a create or update form through the web interface
- **THEN** the system validates the request using the same rules as the API (StoreRecetteRequest / UpdateRecetteRequest)

#### Scenario: Web routes use existing authorization
- **WHEN** a user attempts to edit or delete a recipe through the web interface
- **THEN** the system checks authorization using the existing RecettePolicy

### Requirement: Dynamic form sections
The system SHALL support dynamic adding and removing of ingredient and step entries in the create and edit forms using JavaScript.

#### Scenario: Add ingredient entry
- **WHEN** the user clicks "Add ingredient"
- **THEN** the system appends a new ingredient entry row with fields for ingredient selection, quantity, and unit

#### Scenario: Remove ingredient entry
- **WHEN** the user clicks "Remove" on an ingredient entry
- **THEN** the system removes that ingredient entry row

#### Scenario: Add step entry
- **WHEN** the user clicks "Add step"
- **THEN** the system appends a new step entry row with fields for step number and instruction

#### Scenario: Remove step entry
- **WHEN** the user clicks "Remove" on a step entry
- **THEN** the system removes that step entry row
