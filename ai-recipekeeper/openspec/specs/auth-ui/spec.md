# auth-ui Specification

## Purpose

Provides the visual presentation layer for user authentication pages (login and register) using Tailwind CSS v4, with consistent design tokens, fonts, and interactive password visibility toggles.

## Requirements

### Requirement: Login page renders with correct design
The system SHALL render the login page using Tailwind CSS v4 with the project's design tokens, Playfair Display and Inter fonts, and Material Symbols icons.

#### Scenario: Login page displays correctly
- **WHEN** a guest visitor navigates to `/login`
- **THEN** the system renders a page with the authentication background image, the AI Recipe Keeper branding, email and password fields, a "Remember me" checkbox, a "Log In" button, and a link to the registration page

#### Scenario: Login page preserves Laravel authentication behavior
- **WHEN** the login page is rendered
- **THEN** the form includes a valid CSRF token, the email field retains old input on validation failure, the password field is present, and validation errors are displayed when applicable

### Requirement: Register page renders with correct design
The system SHALL render the registration page using Tailwind CSS v4 with the project's design tokens, Playfair Display and Inter fonts, and Material Symbols icons.

#### Scenario: Register page displays correctly
- **WHEN** a guest visitor navigates to `/register`
- **THEN** the system renders a page with the authentication background image, the AI Recipe Keeper branding, name, email, password, and password confirmation fields, a "Create Account" button, and a link to the login page

#### Scenario: Register page preserves Laravel registration behavior
- **WHEN** the registration page is rendered
- **THEN** the form includes a valid CSRF token, name/email fields retain old input on validation failure, password and password confirmation fields are present, and validation errors are displayed when applicable

### Requirement: Password visibility toggle on login
The system SHALL provide a working password visibility toggle on the login page password field.

#### Scenario: Toggle password visibility on login
- **WHEN** a user clicks the visibility toggle button on the login password field
- **THEN** the input type changes from `password` to `text` and the icon changes from `visibility_off` to `visibility`

#### Scenario: Toggle password back to hidden on login
- **WHEN** a user clicks the visibility toggle button again when the password is visible
- **THEN** the input type changes from `text` to `password` and the icon changes from `visibility` to `visibility_off`

### Requirement: Password visibility toggles on register
The system SHALL provide working password visibility toggles on both the password and password confirmation fields of the registration page.

#### Scenario: Toggle password visibility on register password field
- **WHEN** a user clicks the visibility toggle button on the register password field
- **THEN** the input type changes from `password` to `text` and the icon changes from `visibility_off` to `visibility`

#### Scenario: Toggle password confirmation visibility on register
- **WHEN** a user clicks the visibility toggle button on the register password confirmation field
- **THEN** the input type changes from `password` to `text` and the icon changes from `visibility_off` to `visibility`

### Requirement: Guest layout uses Tailwind CSS
The system SHALL load Tailwind CSS v4 via the existing Vite build for authentication pages, replacing the Bootstrap CDN.

#### Scenario: Guest layout loads Vite assets
- **WHEN** a guest layout page is rendered
- **THEN** the page loads CSS from the Vite build (via `@vite` directive) and does NOT load Bootstrap CDN CSS

#### Scenario: Guest layout does not load Bootstrap
- **WHEN** a guest layout page is rendered
- **THEN** no Bootstrap CSS or JavaScript CDN links are present in the HTML

### Requirement: Authentication background image
The system SHALL display a local authentication background image on login and register pages.

#### Scenario: Background image is served locally
- **WHEN** the login or register page is rendered
- **THEN** the background image is loaded from `public/images/auth/image.png` using Laravel's `asset()` helper

#### Scenario: No external image URL is used
- **WHEN** the login or register page HTML is inspected
- **THEN** no external Google or third-party image URLs are present in the background-image CSS property

### Requirement: Authentication-specific styling
The system SHALL use authentication-specific styles that do not affect other layouts.

#### Scenario: Auth styles are scoped
- **WHEN** authentication pages are rendered
- **THEN** custom styles (design tokens, font imports, shadows) are applied only to the guest layout and auth views, not to the app layout or admin layout
