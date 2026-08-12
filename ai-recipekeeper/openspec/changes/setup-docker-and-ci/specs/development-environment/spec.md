## Purpose

Provides a containerized local development environment for the Laravel application and a GitHub Actions CI pipeline that validates every push and pull request without external services.

## ADDED Requirements

### Requirement: Docker-based local development environment
The system SHALL provide a containerized development environment, startable with a single command from the repository root, that runs the application with MySQL, Redis, and a frontend asset server without requiring host-installed PHP, Node, MySQL, or Redis.

#### Scenario: Start the environment
- **WHEN** a developer runs the documented Docker start command from the repository root
- **THEN** the application becomes available at `http://localhost:8000`
- **AND** the frontend asset development server becomes available at `http://localhost:5173`

#### Scenario: Health endpoint responds
- **WHEN** the application container is running
- **THEN** the `/up` health endpoint responds successfully

#### Scenario: Database state persists
- **WHEN** the environment is stopped and started again
- **THEN** MySQL data written before the restart remains available

#### Scenario: Vite asset wiring
- **WHEN** a developer opens an application page served by the containerized environment with the asset server running
- **THEN** the page references assets served by the asset development server with hot reload enabled

### Requirement: Containerized service wiring
The system SHALL configure the containerized application to reach MySQL and Redis through the Docker network service names, SHALL use Redis as the queue connection in the Docker environment, and SHALL NOT require a host MySQL or Redis instance.

#### Scenario: Database connection inside containers
- **WHEN** the application container connects to the database
- **THEN** it connects to the MySQL container by its service name and uses the credentials provided through environment variables

#### Scenario: Redis queue connection inside containers
- **WHEN** the application dispatches a queue job in the Docker environment
- **THEN** the job is sent to the Redis container by its service name on the `generations` queue

### Requirement: Queue worker in Docker environment
The system SHALL run a queue worker in the Docker environment that processes the `generations` queue and SHALL keep generation status tracking consistent (`pending` → `processing` → `completed`/`failed`).

#### Scenario: Worker processes generation jobs
- **WHEN** a user submits an AI recipe generation request and the worker container is running
- **THEN** the worker processes the job on the `generations` queue
- **AND** the generation record reaches `completed` on success or `failed` with an error message on permanent failure

#### Scenario: Web requests return immediately
- **WHEN** a user submits an AI recipe generation request
- **THEN** the web request returns immediately with an accepted response regardless of how long the queued job takes

### Requirement: Frontend production build
The system SHALL support a production asset build that produces the compiled CSS and JavaScript the application serves when the asset development server is not running.

#### Scenario: Production build succeeds
- **WHEN** the production build command runs on a machine with a compatible Node version and installed dependencies
- **THEN** the build completes successfully and produces the compiled assets
- **AND** the application serves the compiled assets

### Requirement: CI pipeline execution
The system SHALL provide a CI pipeline that runs automatically on pushes to the repository's tracked branches and on pull requests, using PHP and the application's locked dependencies.

#### Scenario: Pipeline triggers
- **WHEN** a push is made to a tracked branch or a pull request is opened against a tracked branch
- **THEN** the CI pipeline runs automatically

#### Scenario: Pipeline steps complete
- **WHEN** the CI pipeline runs on a valid commit
- **THEN** the locked Composer dependencies install successfully
- **AND** the frontend production build succeeds
- **AND** the lint quality gate passes
- **AND** the full test suite passes

### Requirement: Toolchain versions in CI
The system SHALL run CI on an up-to-date Ubuntu runner with PHP and Node versions compatible with the application's requirements.

#### Scenario: PHP version compatible with the application
- **WHEN** CI installs PHP
- **THEN** the PHP version satisfies the application's declared requirement
- **AND** the required PHP extensions are available

#### Scenario: Node version compatible with frontend tooling
- **WHEN** CI installs Node
- **THEN** the Node version satisfies the frontend build tooling requirements

### Requirement: CI without external services
The system SHALL run the CI test suite without MySQL, Redis, or external AI provider access.

#### Scenario: Tests run on SQLite in-memory
- **WHEN** the CI test suite runs
- **THEN** the suite uses the testing configuration (SQLite in-memory database, sync queue, array cache, array sessions)
- **AND** no MySQL or Redis service is required for the suite to pass

#### Scenario: Tests run without an AI API key
- **WHEN** the CI test suite runs on a fresh environment without an AI provider API key
- **THEN** the suite passes
- **AND** no real external AI API call is made during the suite

### Requirement: Secret protection
The system SHALL keep credentials, API keys, and environment files out of version control and out of CI artifacts.

#### Scenario: Environment files ignored
- **WHEN** a developer creates a local environment file or API key values are present locally
- **THEN** they are excluded from version control

#### Scenario: No secrets in pipeline output
- **WHEN** the CI pipeline runs
- **THEN** no secrets or API keys are printed in logs or exposed in pipeline artifacts

### Requirement: CI cache for dependencies
The system SHALL cache dependency downloads in CI to speed up repeated runs.

#### Scenario: Dependency cache reused
- **WHEN** CI installs Composer and npm dependencies
- **THEN** downloads are cached and reused across runs when the lock files are unchanged

## MODIFIED Requirements

None.