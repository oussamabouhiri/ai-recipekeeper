## MODIFIED Requirements

### Requirement: Dashboard navigation
The system SHALL provide desktop top navigation and mobile bottom navigation where every link uses an existing route, including a link to the browse recipes page.

#### Scenario: Desktop navigation links
- **WHEN** an authenticated user views the dashboard on a desktop viewport
- **THEN** the top navigation shows the application brand, Dashboard, Browse (linking to `/browse`), Recipes (linking to `/recipes`), Generate with AI, and My Favorites links to their existing routes, plus a Create Recipe action linking to `/recipes/create`

#### Scenario: Mobile bottom navigation
- **WHEN** an authenticated user views the dashboard on a mobile viewport
- **THEN** the system displays a fixed bottom navigation with Dashboard, Browse (linking to `/browse`), AI Generator, and Favorites items linking to their existing routes

#### Scenario: User identity and logout
- **WHEN** an authenticated user views the dashboard
- **THEN** the dashboard displays the user's real name or initials, and the logout action uses the application's existing logout mechanism

#### Scenario: No unsupported nav features
- **WHEN** the dashboard navigation renders
- **THEN** it does not include notifications or settings controls, because no such backend features exist
