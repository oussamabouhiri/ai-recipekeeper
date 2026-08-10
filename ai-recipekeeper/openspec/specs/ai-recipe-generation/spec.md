# ai-recipe-generation Specification

## Purpose

Provides asynchronous AI recipe generation by sending user-provided ingredients and preferences to OpenRouter, parsing the structured AI response, and transactionally creating a complete recipe with steps, ingredients, and categories.

## Requirements

### Requirement: Generation request validation
The system SHALL validate the generation request payload and reject invalid ones with a 422 response.

#### Scenario: Missing ingredients rejected
- **WHEN** a generation request has no `ingredients` field
- **THEN** the system responds with a 422 validation error

#### Scenario: Empty ingredients array rejected
- **WHEN** a generation request has an `ingredients` array with fewer than 1 entry
- **THEN** the system responds with a 422 validation error

#### Scenario: Too many ingredients rejected
- **WHEN** a generation request has an `ingredients` array with more than 20 entries
- **THEN** the system responds with a 422 validation error

#### Scenario: Missing ingredient name rejected
- **WHEN** a generation request has an ingredient entry without a `name` field
- **THEN** the system responds with a 422 validation error

#### Scenario: Invalid difficulty rejected
- **WHEN** a generation request has a `difficulty` value other than `easy`, `medium`, or `hard`
- **THEN** the system responds with a 422 validation error

#### Scenario: Invalid category rejected
- **WHEN** a generation request references a category id that does not exist
- **THEN** the system responds with a 422 validation error

#### Scenario: Invalid servings rejected
- **WHEN** a generation request has a `servings` value less than 1 or greater than 100
- **THEN** the system responds with a 422 validation error

### Requirement: Generation creation
The system SHALL allow authenticated users to create a generation request and SHALL return immediately with a 202 Accepted response.

#### Scenario: Authenticated user creates generation
- **WHEN** an authenticated user submits a valid generation request
- **THEN** the system creates a `GenerationIa` record with `status` = `pending`
- **AND** dispatches a generation job to the queue
- **AND** returns a 202 response containing the generation id and status

#### Scenario: Unauthenticated creation rejected
- **WHEN** an unauthenticated visitor submits a generation request
- **THEN** the system rejects the request with a 401 response

#### Scenario: Generation prompt stored
- **WHEN** a generation is created
- **THEN** the system stores a serialized representation of the input (ingredients, preferences, constraints, servings, difficulty) as the `prompt` on the `GenerationIa` record

### Requirement: AI provider integration
The system SHALL call OpenRouter's API to generate recipe data from the user's input.

#### Scenario: Server constructs the prompt
- **WHEN** the generation job processes
- **THEN** the system constructs a prompt that instructs the AI to return structured JSON containing title, description, prep_time, cook_time, servings, difficulty, ingredients (with name, quantity, unit), categories, and etapes (with step_number and instruction)

#### Scenario: AI response is structured JSON
- **WHEN** the system sends a prompt to OpenRouter
- **THEN** the system expects a JSON response containing the required fields

#### Scenario: API key from environment
- **WHEN** the system calls OpenRouter
- **THEN** the API key is read from the `OPENROUTER_API_KEY` environment variable
- **AND** the API key is never exposed in API responses, logs, or error messages

#### Scenario: Default model from configuration
- **WHEN** the system calls OpenRouter
- **THEN** the model used is determined by server-side configuration, not by client input

### Requirement: AI response validation
The system SHALL validate the AI response before creating any database records.

#### Scenario: Valid AI response
- **WHEN** the AI response contains all required fields with correct types
- **THEN** the system proceeds to create the recipe

#### Scenario: Invalid AI response rejected
- **WHEN** the AI response is missing required fields, has invalid types, or is not valid JSON
- **THEN** the system marks the generation as `failed` with a descriptive error message
- **AND** no recipe or related records are created

#### Scenario: Partial AI response rejected
- **WHEN** the AI response has a title but is missing etapes or ingredients
- **THEN** the system marks the generation as `failed`
- **AND** no recipe or related records are created

### Requirement: Recipe creation from AI response
The system SHALL create a complete recipe transactionally from the validated AI response.

#### Scenario: Recipe created with AI flag
- **WHEN** the AI response is validated
- **THEN** the system creates a `Recette` record with `user_id` = the authenticated user, `is_ai_generated` = true, and `statut` = `published`

#### Scenario: Steps created in order
- **WHEN** the AI response contains etapes
- **THEN** the system creates `Etape` records with correct `step_number` and `instruction` values

#### Scenario: Ingredients matched or created
- **WHEN** the AI response contains ingredients
- **THEN** the system matches each ingredient by name (case-insensitive) or creates it if it does not exist
- **AND** attaches each ingredient to the recipe with the provided `quantity` and `unit` on the pivot

#### Scenario: Categories attached
- **WHEN** the request includes category IDs
- **THEN** the system attaches those categories to the generated recipe

#### Scenario: Transactional creation
- **WHEN** the system creates the recipe and its relationships
- **THEN** all database writes occur within a single transaction
- **AND** if any write fails, all changes are rolled back and the generation is marked as `failed`

### Requirement: Generation status tracking
The system SHALL track the processing status of generation requests through the `GenerationIa` model.

#### Scenario: Status transitions on success
- **WHEN** a generation job starts processing
- **THEN** the system updates the status to `processing` and records `started_at`
- **AND** when the recipe is created successfully, the system updates the status to `completed` and records `completed_at`

#### Scenario: Status transition on failure
- **WHEN** a generation job fails permanently (all retries exhausted or immediate failure)
- **THEN** the system updates the status to `failed`
- **AND** records the error message in `error_message`

#### Scenario: Job ID stored
- **WHEN** a generation job is dispatched
- **THEN** the system stores the job ID in the `generation_ia.job_id` column

### Requirement: Generation status endpoint
The system SHALL expose an endpoint to check the status of a generation request.

#### Scenario: Owner views generation status
- **WHEN** the generation owner requests `GET /api/generations/{id}`
- **THEN** the system returns the generation status, model used, timestamps, and error message (if failed)

#### Scenario: Completed generation includes recipe
- **WHEN** a generation has status `completed`
- **THEN** the response includes the generated recipe data

#### Scenario: Non-owner access denied
- **WHEN** a user who is neither the owner nor an admin requests `GET /api/generations/{id}`
- **THEN** the system responds with a 404 response

#### Scenario: Admin views any generation
- **WHEN** an admin requests `GET /api/generations/{id}`
- **THEN** the system returns the generation regardless of ownership

### Requirement: Generation listing endpoint
The system SHALL expose an endpoint to list the authenticated user's generations.

#### Scenario: User lists own generations
- **WHEN** an authenticated user requests `GET /api/generations`
- **THEN** the system returns only that user's generations

#### Scenario: Admin lists all generations
- **WHEN** an admin requests `GET /api/generations`
- **THEN** the system returns all generations

#### Scenario: Unauthenticated listing rejected
- **WHEN** an unauthenticated visitor requests `GET /api/generations`
- **THEN** the system responds with a 401 response

### Requirement: Generation authorization
The system SHALL enforce owner-or-admin access control on generation resources.

#### Scenario: Owner access
- **WHEN** the generation owner requests their own generation
- **THEN** the system allows access

#### Scenario: Admin access
- **WHEN** an admin requests any generation
- **THEN** the system allows access

#### Scenario: Non-owner denied
- **WHEN** a user who is neither the owner nor an admin requests a generation
- **THEN** the system denies access with a 404 response

### Requirement: Generation retry behavior
The system SHALL use the existing queue retry infrastructure for transient failures.

#### Scenario: Transient API failure retried
- **WHEN** the OpenRouter API call fails due to a network or timeout error
- **THEN** the job retries with the configured backoff schedule

#### Scenario: Permanent failure not retried
- **WHEN** the AI response is invalid JSON or fails validation
- **THEN** the job fails immediately without retrying

#### Scenario: All retries exhausted
- **WHEN** all retry attempts are exhausted
- **THEN** the generation status is set to `failed` with the error message

### Requirement: Generation API routes
The system SHALL expose generation endpoints under the `auth:sanctum` middleware group.

#### Scenario: Create generation route
- **WHEN** a client sends `POST /api/generate` with a valid payload and authentication
- **THEN** the system processes the generation request

#### Scenario: Show generation route
- **WHEN** a client sends `GET /api/generations/{generation}` with authentication
- **THEN** the system returns the generation status

#### Scenario: List generations route
- **WHEN** a client sends `GET /api/generations` with authentication
- **THEN** the system returns the user's generations
