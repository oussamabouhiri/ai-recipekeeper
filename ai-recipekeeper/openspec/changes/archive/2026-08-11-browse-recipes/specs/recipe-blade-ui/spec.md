## MODIFIED Requirements

### Requirement: Authenticated app layout
The system SHALL provide a shared authenticated layout (`layouts/app.blade.php`) with a responsive navbar, the authenticated user's name, and navigation links to the dashboard, browse recipes, and recipe list.

#### Scenario: Layout renders for authenticated user
- **WHEN** an authenticated user navigates to any recipe page
- **THEN** the system renders the page within a shared layout that includes a navbar with the application name, the user's name, and a logout button

#### Scenario: Layout excludes guest users
- **WHEN** a guest visits any recipe page
- **THEN** the system redirects the guest to the login page
