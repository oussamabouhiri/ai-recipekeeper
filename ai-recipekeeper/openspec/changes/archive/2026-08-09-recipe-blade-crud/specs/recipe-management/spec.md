## MODIFIED Requirements

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
