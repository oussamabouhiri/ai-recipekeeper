## Purpose

Provides user authentication for AI Recipe Keeper: session-based web registration and login/logout, plus trusted API access through Sanctum bearer tokens.

## ADDED Requirements

### Requirement: User registration
The system SHALL allow anonymous visitors to create a user account with a name, a unique email address, and a password.

#### Scenario: Successful registration
- **WHEN** an anonymous visitor submits a valid registration form
- **THEN** the system creates a user account with role `user`

#### Scenario: Duplicate email registration
- **WHEN** an anonymous visitor registers with an email already in use
- **THEN** the system rejects the registration and shows a validation error on the email field

#### Scenario: Validation failure
- **WHEN** the registration form is submitted with an invalid email or a password shorter than the minimum required length
- **THEN** the system shows validation errors and does not create an account

### Requirement: Web login
The system SHALL allow existing users to log in with their email and password using the web session guard.

#### Scenario: Successful login
- **WHEN** a registered user submits valid credentials
- **THEN** the system authenticates the user and grants access to authenticated-only pages
- **AND** the user session is persisted

#### Scenario: Failed login
- **WHEN** a user submits invalid credentials
- **THEN** the system shows an authentication error and does not start a session

### Requirement: Web logout
The system SHALL allow authenticated web users to log out.

#### Scenario: Logout
- **WHEN** an authenticated user logs out
- **THEN** the system invalidates the session and redirects the user to the login page
- **AND** the user can no longer access authenticated-only pages until logging in again

### Requirement: API token authentication
The system SHALL provide bearer-token authentication for API access using Sanctum.

#### Scenario: Token creation
- **WHEN** an authenticated user requests an API token
- **THEN** the system issues a one-time plain-text bearer token bound to that user

#### Scenario: Token-protected API access
- **WHEN** a client sends a valid bearer token to a Sanctum-protected API route
- **THEN** the system authenticates the request as the token's user

#### Scenario: Invalid token rejection
- **WHEN** a client sends a missing, malformed, or revoked token
- **THEN** the system rejects the request with an authentication error (401)