## MODIFIED Requirements

### Requirement: Web login
The system SHALL allow existing users to log in with their email and password using the web session guard.

#### Scenario: Successful login
- **WHEN** a registered user submits valid credentials
- **THEN** the system authenticates the user and grants access to authenticated-only pages
- **AND** the user session is persisted

#### Scenario: Failed login
- **WHEN** a user submits invalid credentials
- **THEN** the system shows an authentication error and does not start a session

#### Scenario: Login form renders correctly
- **WHEN** a guest navigates to `/login`
- **THEN** the system renders the login page with email field, password field, remember checkbox, CSRF token, and validation error display

## ADDED Requirements

### Requirement: Root route redirect by authentication state
The system SHALL redirect visitors at the root URL (`/`) based on their authentication state.

#### Scenario: Guest visitor at root redirects to login
- **WHEN** an unauthenticated visitor navigates to `/`
- **THEN** the system redirects to `/login`

#### Scenario: Authenticated user at root redirects to dashboard
- **WHEN** an authenticated user navigates to `/`
- **THEN** the system redirects to `/dashboard`
