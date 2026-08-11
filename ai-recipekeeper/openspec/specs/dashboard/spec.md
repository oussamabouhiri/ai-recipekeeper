# dashboard Specification

## Purpose

Provides a polished, data-driven dashboard page at `/dashboard` that greets the authenticated user by name, showcases a real featured recipe, and connects users to the existing recipes, favorites, and AI generation flows using real backend data only.

## Requirements

### Requirement: Authenticated dashboard access
The system SHALL serve the dashboard at `GET /dashboard` to authenticated users only, redirecting guests to the login page according to the existing authentication behavior.

#### Scenario: Authenticated user can access dashboard
- **WHEN** an authenticated user navigates to `/dashboard`
- **THEN** the system responds successfully and renders the dashboard page

#### Scenario: Guest is redirected to login
- **WHEN** a guest navigates to `/dashboard`
- **THEN** the system redirects the guest to the login page

### Requirement: Dashboard data comes from the backend
The system SHALL prepare all dashboard data server-side using existing models, relationships, and the recipe visibility rules, so no dashboard value is hard-coded or invented.

#### Scenario: Dashboard shows real authenticated user
- **WHEN** an authenticated user views the dashboard
- **THEN** the system displays the user's real name (or initials derived from it), never a placeholder

#### Scenario: Featured recipe respects visibility
- **WHEN** the dashboard selects a featured recipe
- **THEN** the system only selects recipes the authenticated user is allowed to see according to the existing `visibleTo()` visibility rules (published recipes, plus the user's own hidden recipes; admins see all)

#### Scenario: Dashboard renders only real recipes
- **WHEN** the dashboard shows recipe data (title, description, category, times, difficulty, image)
- **THEN** every value comes from the recipes table and its relationships; no fictional recipes are displayed

### Requirement: Welcome hero section
The system SHALL render a welcome hero on the dashboard with a time-based greeting, the authenticated user's name, and actions linking to existing routes.

#### Scenario: Time-based greeting
- **WHEN** an authenticated user views the dashboard
- **THEN** the system displays "Good Morning", "Good Afternoon", or "Good Evening" based on the server's current time, along with the user's real name

#### Scenario: Hero actions link to existing routes
- **WHEN** the user clicks "Generate with AI"
- **THEN** the system navigates to the existing `/generations/create` route

#### Scenario: Create recipe action
- **WHEN** the user clicks "Create Manual Recipe"
- **THEN** the system navigates to the existing `/recipes/create` route

### Requirement: Featured recipe card
The system SHALL display a prominent featured recipe card backed by a real recipe visible to the user, preferring the latest published recipe, linking to the recipe detail page.

#### Scenario: Featured card shows real recipe data
- **WHEN** the dashboard renders the featured recipe card
- **THEN** the card shows the recipe's real image, title, description, at least one category, preparation and cooking time, and difficulty

#### Scenario: Featured card links to recipe detail
- **WHEN** the user clicks the featured recipe card or its action
- **THEN** the system navigates to the existing `GET /recipes/{recipe}` route for that recipe

#### Scenario: No recipe available
- **WHEN** no recipe is visible to the authenticated user
- **THEN** the system renders a polished empty state with a link to create a recipe instead of an empty card

### Requirement: Rating and review presentation
The system SHALL display only real review information on the dashboard, using review counts and averages computed from the `avis` table, with a proper empty state when no reviews exist.

#### Scenario: Recipe has reviews
- **WHEN** the featured recipe has one or more reviews
- **THEN** the dashboard shows the real average rating and real review count derived from those reviews

#### Scenario: Recipe has no reviews
- **WHEN** the featured recipe has no reviews
- **THEN** the dashboard displays a "No reviews yet" empty state and does not display stars or review counts

### Requirement: Favorites presentation
The system SHALL represent the user's favorites on the dashboard using only real data from the `favoris` table, with a proper empty state when the user has no favorites.

#### Scenario: User has favorites
- **WHEN** the authenticated user has favorited recipes
- **THEN** the dashboard reflects the real favorited recipes and links to the existing `/favorites` page

#### Scenario: User has no favorites
- **WHEN** the authenticated user has no favorites
- **THEN** the dashboard displays a friendly empty state (e.g., "You haven't saved any favorites yet.") without inventing favorites

### Requirement: AI inspiration area
The system SHALL provide an AI-related area on the dashboard that routes users into the existing generation workflow without pretending to accept free-text input the backend does not support.

#### Scenario: AI card links to generation page
- **WHEN** the user interacts with the AI inspiration area
- **THEN** the system navigates to the existing `/generations/create` route, where the structured generation form is used

#### Scenario: No fake AI submission
- **WHEN** the dashboard renders the AI area
- **THEN** the dashboard does not submit to the generation endpoint with data the backend cannot accept, and does not display fake generation results

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

### Requirement: Recipe image display
The system SHALL display recipe images served from web-accessible files referenced by each recipe's `image_path`, and SHALL make the 25 real recipe images web-accessible under `public/images/recipes/` with URL-safe filenames.

#### Scenario: Featured card uses recipe image_path
- **WHEN** the featured recipe has an `image_path`
- **THEN** the card renders the image from `public/images/recipes/` via that path and uses the recipe title as alt text

#### Scenario: Image file missing
- **WHEN** a recipe's `image_path` is null or the referenced file does not exist
- **THEN** the dashboard renders a placeholder instead of a broken image

#### Scenario: All seeded recipes have images
- **WHEN** the seeded recipes' `image_path` values are inspected
- **THEN** each of the 25 seeded recipes points to a real, web-accessible file corresponding to its title

#### Scenario: Mockup image excluded
- **WHEN** images are made web-accessible
- **THEN** `honey-glazed-salamon-with-roasted-carrots.png` is not referenced by any recipe because no matching recipe exists

### Requirement: Dashboard footer
The system SHALL render a footer containing only branding and links to existing routes.

#### Scenario: Footer uses existing routes
- **WHEN** the dashboard footer renders
- **THEN** it includes the application brand and only navigation links that map to existing routes; it does not link to non-existent pages such as Privacy Policy, Terms, Help Center, or Feedback

### Requirement: Responsive and accessible rendering
The system SHALL render the dashboard responsively on desktop, tablet, and mobile viewports using semantic HTML and accessible controls, without horizontal overflow.

#### Scenario: Mobile layout stacks content
- **WHEN** the dashboard is viewed on a mobile viewport
- **THEN** content stacks vertically, cards span the full width, typography remains readable, and the bottom navigation is visible without horizontal overflow

#### Scenario: Accessible controls
- **WHEN** the dashboard renders
- **THEN** it uses semantic HTML, real links/buttons, visible keyboard focus states, sufficient contrast, and descriptive alt text for images

### Requirement: Dashboard tests
The system SHALL include feature tests covering dashboard behavior.

#### Scenario: Tests verify dashboard requirements
- **WHEN** the test suite runs
- **THEN** tests verify authenticated access, guest redirection, real user data, real recipe data, empty review/favorite states, recipe image paths, valid route links, and that existing authentication and recipe functionality remains unaffected
