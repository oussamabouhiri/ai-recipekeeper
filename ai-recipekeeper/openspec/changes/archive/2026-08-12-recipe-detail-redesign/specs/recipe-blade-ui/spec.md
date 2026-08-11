# recipe-blade-ui Specification

## MODIFIED Requirements

### Requirement: Authenticated app layout
The system SHALL provide a shared authenticated layout (`layouts/dashboard.blade.php`) with a desktop top navigation bar and a mobile bottom navigation bar, the app brand, a Create Recipe action, the authenticated user's name, a logout action, and navigation links to the dashboard, browse recipes, my recipes, the AI generator, and my favorites.

#### Scenario: Layout renders for authenticated user
- **WHEN** an authenticated user navigates to any recipe page
- **THEN** the system renders the page within the shared dashboard layout that includes the desktop top navigation, the mobile bottom navigation, the footer, the user's name, and a logout action

#### Scenario: Layout excludes guest users
- **WHEN** a guest visits any recipe page
- **THEN** the system redirects the guest to the login page

### Requirement: Recipe detail page
The system SHALL provide a Blade page at `/recipes/{recipe}` showing the full details of a recipe, rendered within the shared dashboard layout using the design system's typography (Playfair Display headings, Inter body), color tokens, and spacing.

#### Scenario: Display published recipe
- **WHEN** an authenticated user views a published recipe
- **THEN** the system displays the title, description, author and creation date, preparation time, cooking time, servings, difficulty, status, categories, ingredients (with quantity and unit), and ordered steps

#### Scenario: Hero image displayed
- **WHEN** an authenticated user views a recipe whose image file exists
- **THEN** the page displays the recipe's image full-width at the top of the page with a hero treatment and a placeholder fallback icon when no image exists

#### Scenario: Metadata bar rendered
- **WHEN** an authenticated user views a recipe
- **THEN** the page displays a metadata bar with the recipe's preparation time, cooking time, servings, and difficulty, omitting any field that has no value

#### Scenario: Rating summary rendered
- **WHEN** an authenticated user views a recipe that has reviews
- **THEN** the page displays the average rating and the review count; when the recipe has no reviews, the page indicates that no reviews exist

#### Scenario: Two-column content layout on desktop
- **WHEN** an authenticated user views a recipe on a desktop viewport (>= 1024px)
- **THEN** the ingredients are presented in a scan-friendly left column and the numbered preparation steps in a wider right column; the step numbers are visually distinct and the instructions remain readable beside the ingredients

#### Scenario: Single-column content layout on mobile
- **WHEN** an authenticated user views a recipe on a mobile viewport (< 1024px)
- **THEN** the ingredients and steps stack in a single column without horizontal scrolling

#### Scenario: Owner views hidden recipe
- **WHEN** the owner views their own hidden recipe
- **THEN** the system displays the recipe with all details and a hidden status indicator

#### Scenario: Non-owner cannot view hidden recipe
- **WHEN** a user who is neither the owner nor an admin views a hidden recipe
- **THEN** the system responds with a 404 page

#### Scenario: Actions for owner
- **WHEN** the owner views their own recipe
- **THEN** the system shows Edit and Delete action buttons for the recipe

#### Scenario: Actions for admin
- **WHEN** an admin views any recipe
- **THEN** the system shows Edit and Delete action buttons for the recipe

#### Scenario: Actions for non-owner
- **WHEN** a user who is neither the owner nor an admin views a recipe
- **THEN** the system does not show Edit or Delete buttons

#### Scenario: Category chips displayed
- **WHEN** an authenticated user views a recipe that has categories
- **THEN** the page displays the recipe's categories as chips; when the recipe has no categories, no chip row is shown