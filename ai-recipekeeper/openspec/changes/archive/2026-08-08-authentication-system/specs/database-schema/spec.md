## MODIFIED Requirements

### Requirement: Users table
The system SHALL maintain a users table for authentication and user management.

#### Scenario: User creation
- **WHEN** a new user registers
- **THEN** the system creates a record with id, name, email, password, email_verified_at, remember_token, created_at, and updated_at fields

#### Scenario: Admin flag
- **WHEN** a user account is created or updated
- **THEN** the system stores an is_admin boolean flag indicating whether the account has the admin role
- **AND** publicly registered accounts default to is_admin false

#### Scenario: `is_admin` flag scope
- **WHEN** an account is designated as admin
- **THEN** only the is_admin flag on the existing users table is changed
- **AND** no new table or join table is created for roles