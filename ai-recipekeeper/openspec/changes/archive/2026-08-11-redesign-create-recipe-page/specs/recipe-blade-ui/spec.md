## MODIFIED Requirements

### Requirement: Create recipe form
The system SHALL provide a Blade form at `/recipes/create` for authenticated users to create a new recipe. The form SHALL use the dashboard layout (`layouts/dashboard.blade.php`) and present a two-column dashboard layout with a centered max-width content container.

#### Scenario: Form renders with dashboard layout
- **WHEN** an authenticated user navigates to the create recipe page
- **THEN** the system renders the page within the dashboard layout (Tailwind + MD3 design system) with the same top navigation, typography (Playfair Display headings, Inter body), and color palette as other dashboard pages

#### Scenario: Form renders with all fields
- **WHEN** an authenticated user navigates to the create recipe page
- **THEN** the system displays a form with fields for title, description, preparation time, cooking time, servings, difficulty, status (published/hidden), categories (chip-based display with add/remove), ingredients (dynamic entries with ingredient dropdown, quantity, and unit), and steps (dynamic entries with step number and instruction)

#### Scenario: Two-column layout on desktop
- **WHEN** an authenticated user views the create recipe page on a desktop viewport (>= 1024px)
- **THEN** the form displays in a two-column layout with Basic Details, Ingredients, and Preparation Steps in the left column (~65-70% width), and Timing & Yield, Categorization, and Save/Cancel actions in a sticky right sidebar (~30-35% width)

#### Scenario: Single-column layout on mobile
- **WHEN** an authenticated user views the create recipe page on a mobile viewport (< 1024px)
- **THEN** the form displays in a single column with all sections stacked vertically, sidebar content appearing below the main content, and no horizontal scrolling

#### Scenario: Header with save and cancel actions
- **WHEN** an authenticated user views the create recipe page
- **THEN** the page header displays "Create a New Recipe" with a subtitle, a Cancel button, and a Save Recipe button, positioned above the form

#### Scenario: Successful creation
- **WHEN** the user submits a valid recipe form
- **THEN** the system creates the recipe with the authenticated user as owner, redirects to the recipe detail page, and displays a success message

#### Scenario: Validation errors displayed
- **WHEN** the user submits an invalid recipe form (e.g., missing title)
- **THEN** the system redisplays the form with validation error messages styled within the dashboard design system next to the invalid fields

#### Scenario: User cannot set user_id
- **WHEN** the user submits the create recipe form
- **THEN** the system ignores any user_id value and sets the owner to the authenticated user
