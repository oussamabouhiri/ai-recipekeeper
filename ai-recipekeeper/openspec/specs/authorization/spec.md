# authorization Specification

## Purpose

Enforces role-based access control in AI Recipe Keeper by distinguishing `user` and `admin` roles and providing the Policy foundation for user-owned resources and admin moderation.

## Requirements

### Requirement: Role distinction
The system SHALL distinguish two roles: `user` and `admin`. Every account is one of these two roles.

#### Scenario: Default role on registration
- **WHEN** a user registers through public registration
- **THEN** the account has role `user`

#### Scenario: Admin role membership
- **WHEN** an account is designated as admin
- **THEN** the account gains access to admin-only functionality

### Requirement: Admin accounts not created by public registration
The system SHALL NOT allow the public registration process to create admin accounts.

#### Scenario: Public registration always creates a user role
- **WHEN** an anonymous visitor registers an account
- **THEN** the new account can never have role `admin` through the registration form itself

### Requirement: Admin-only access control
The system SHALL restrict admin-only functionality to admin users through an admin middleware.

#### Scenario: Admin accesses admin route
- **WHEN** an admin user requests an admin-only route
- **THEN** the request is allowed

#### Scenario: Non-admin user rejected
- **WHEN** a user without the admin role requests an admin-only route
- **THEN** the request is denied with a forbidden response (403) or redirect

#### Scenario: Guest rejected
- **WHEN** an unauthenticated visitor requests an admin-only route
- **THEN** the visitor is redirected to the login page

### Requirement: Authorization foundation for user resources
The system SHALL provide Policies as the authorization foundation so users only access resources they are authorized to access.

#### Scenario: Authorization via policies
- **WHEN** a user attempts to act on a user-owned resource
- **THEN** the system evaluates the action against the resource's Policy and allows it only when the user is authorized

#### Scenario: Generic policy foundation for moderation
- **WHEN** a future capability introduces a moderated resource
- **THEN** the authorization layer SHALL ship a Policy allowing admin users to moderate user-owned resources
- **AND** the policy foundation SHALL support ownership checks so users cannot modify other users' resources

### Requirement: Favorite resource authorization
The system SHALL provide a Policy for the favorite resource so users only remove favorites they are authorized to remove, and admins can moderate any favorite.

#### Scenario: Owner removes own favorite
- **WHEN** the owner of a favorite requests its removal
- **THEN** the Policy allows the action

#### Scenario: Admin moderates any favorite
- **WHEN** an admin requests the removal of any favorite
- **THEN** the Policy allows the action

#### Scenario: Non-owner removal denied
- **WHEN** a user who is neither the favorite owner nor an admin requests its removal
- **THEN** the Policy denies the action and the system rejects the request with a 403 response
