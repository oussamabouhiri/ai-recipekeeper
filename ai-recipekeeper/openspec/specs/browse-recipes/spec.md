# browse-recipes Specification

## Purpose

Lets users discover and browse recipes through a dedicated search-and-filter page with recipe cards, category filtering, and inline favorite toggling, serving as the primary recipe discovery surface.

## Requirements

### Requirement: Browse page access
The system SHALL serve a browse recipes page at `GET /browse` to both authenticated users and guests, displaying published recipes visible to the current user according to the existing visibility rules.

#### Scenario: Authenticated user accesses browse page
- **WHEN** an authenticated user navigates to `/browse`
- **THEN** the system responds with a 200 status and renders the browse page

#### Scenario: Guest accesses browse page
- **WHEN** a guest navigates to `/browse`
- **THEN** the system responds with a 200 status and renders the browse page showing only published recipes

#### Scenario: Browse page uses dashboard layout
- **WHEN** the browse page renders
- **THEN** the page uses the `layouts/dashboard.blade.php` layout with the modern Tailwind/MD3 design system, desktop top navigation, and mobile bottom navigation

### Requirement: Recipe card display
The system SHALL display each recipe as a card in a responsive grid, showing the recipe image (with fallback), difficulty badge, title, description, preparation time, cooking time, and servings.

#### Scenario: Recipe card shows image
- **WHEN** a recipe has a valid `image_path` and the file exists on disk
- **THEN** the card displays the recipe image with the recipe title as alt text

#### Scenario: Recipe card shows fallback when no image
- **WHEN** a recipe's `image_path` is null or the referenced file does not exist
- **THEN** the card displays a Material Symbols restaurant icon as a placeholder

#### Scenario: Recipe card shows difficulty
- **WHEN** a recipe card renders
- **THEN** the card displays the recipe's difficulty value as a badge overlay on the image area

#### Scenario: Recipe card shows title and description
- **WHEN** a recipe card renders
- **THEN** the card displays the recipe title (line-clamped to 2 lines) and description (line-clamped to 2 lines)

#### Scenario: Recipe card shows time and servings metadata
- **WHEN** a recipe card renders
- **THEN** the card displays prep time, cook time, and servings with Material Symbols icons, omitting any field that is null

#### Scenario: Recipe card links to detail page
- **WHEN** a user clicks a recipe card
- **THEN** the system navigates to the existing `GET /recipes/{recipe}` route for that recipe

#### Scenario: Cards maintain consistent height
- **WHEN** multiple recipe cards render in the same grid row
- **THEN** all cards in the row have equal height using flexbox or grid alignment

### Requirement: Responsive recipe grid
The system SHALL display recipe cards in a responsive grid that adapts to viewport width.

#### Scenario: Mobile viewport
- **WHEN** the browse page is viewed on a viewport narrower than 640px
- **THEN** the recipe grid displays 1 card per row

#### Scenario: Tablet viewport
- **WHEN** the browse page is viewed on a viewport between 640px and 1024px
- **THEN** the recipe grid displays 2 cards per row

#### Scenario: Desktop viewport
- **WHEN** the browse page is viewed on a viewport between 1024px and 1280px
- **THEN** the recipe grid displays 3 cards per row

#### Scenario: Large desktop viewport
- **WHEN** the browse page is viewed on a viewport wider than 1280px
- **THEN** the recipe grid displays 4 cards per row

### Requirement: Recipe search
The system SHALL provide a search input that filters recipes by title and description using server-side LIKE queries, with the search parameter preserved across pagination.

#### Scenario: Search by title
- **WHEN** a user enters a search term and submits the search form
- **THEN** the system returns recipes whose title contains the search term (case-insensitive)

#### Scenario: Search by description
- **WHEN** a user enters a search term that does not match any recipe title
- **THEN** the system returns recipes whose description contains the search term

#### Scenario: Empty search returns all recipes
- **WHEN** a user submits the search form with an empty search field
- **THEN** the system returns all visible recipes as if no search was performed

#### Scenario: No results found
- **WHEN** a search term matches no recipes
- **THEN** the system displays an empty state message indicating no recipes were found

#### Scenario: Search preserves through pagination
- **WHEN** a user navigates to page 2 of search results
- **THEN** the search parameter remains in the URL query string and results remain filtered

### Requirement: Category filtering
The system SHALL display category filter chips that allow users to filter recipes by a single category, with an "All" option to clear the filter.

#### Scenario: Category chips display
- **WHEN** the browse page renders
- **THEN** the system displays a horizontal row of category chips: "All" (always first), followed by all categories from the database ordered by name

#### Scenario: Filter by category
- **WHEN** a user clicks a category chip (other than "All")
- **THEN** the system returns only recipes that belong to the selected category

#### Scenario: "All" clears filter
- **WHEN** a user clicks the "All" chip
- **THEN** the system returns all visible recipes regardless of category

#### Scenario: Active category highlighted
- **WHEN** a category filter is active
- **THEN** the corresponding chip is visually distinguished from inactive chips

#### Scenario: Category filter preserves through pagination
- **WHEN** a user navigates to page 2 while a category filter is active
- **THEN** the category parameter remains in the URL query string and results remain filtered

#### Scenario: Category filter combines with search
- **WHEN** a user has both a search term and a category filter active
- **THEN** the system returns recipes that match both the search term and the category

### Requirement: Favorite toggle on browse cards
The system SHALL display a favorite toggle button on each recipe card for authenticated users, using the existing server-side favorite mechanism.

#### Scenario: Authenticated user sees favorite button
- **WHEN** an authenticated user views a browse card for a recipe they have not favorited
- **THEN** the card displays an outline heart icon button

#### Scenario: Favorited recipe shows filled heart
- **WHEN** an authenticated user views a browse card for a recipe they have favorited
- **THEN** the card displays a filled heart icon button

#### Scenario: Toggle favorite via form submission
- **WHEN** an authenticated user clicks the favorite button on a card
- **THEN** the system submits a form POST (to add) or DELETE (to remove) to the existing favorites endpoint and redirects back to the browse page preserving current search/filter/pagination state

#### Scenario: Guest sees no favorite button
- **WHEN** a guest views the browse page
- **THEN** the recipe cards do not display a favorite button

#### Scenario: Favorite state avoids N+1 queries
- **WHEN** the browse page loads with multiple recipe cards
- **THEN** the system determines favorite status for all visible recipes in a single query, not per-card queries

### Requirement: Browse page pagination
The system SHALL paginate browse results using Laravel's built-in pagination, showing 12 recipes per page, with pagination links preserving search and filter parameters.

#### Scenario: Pagination displays
- **WHEN** more than 12 recipes match the current filters
- **THEN** the system displays pagination links below the recipe grid

#### Scenario: Default page size
- **WHEN** the browse page loads
- **THEN** the system displays up to 12 recipes on the first page

#### Scenario: Pagination preserves query parameters
- **WHEN** a user clicks a pagination link while search or category filters are active
- **THEN** the URL retains the search and category parameters and results remain filtered

### Requirement: Browse page header
The system SHALL display a header section with a page title and a link to the AI recipe generator.

#### Scenario: Page title
- **WHEN** the browse page renders
- **THEN** the system displays a heading such as "Discover Your Next Meal" and a supporting description

#### Scenario: AI generator link
- **WHEN** the browse page renders
- **THEN** the system displays a "Generate with AI" button linking to the existing `/generations/create` route

### Requirement: Browse page empty state
The system SHALL display a friendly empty state when no recipes match the current filters or when no recipes exist.

#### Scenario: No recipes exist
- **WHEN** the database contains no published recipes visible to the user
- **THEN** the system displays an empty state with a message and a link to create a recipe

#### Scenario: Search/filter returns no results
- **WHEN** a search or category filter returns no matching recipes
- **THEN** the system displays an empty state indicating no recipes were found, with an option to clear filters

### Requirement: Browse page navigation
The system SHALL include the Browse page in the application's desktop and mobile navigation, and SHALL keep the existing My Recipes page accessible.

#### Scenario: Desktop nav includes Browse link
- **WHEN** an authenticated user views the browse page on a desktop viewport
- **THEN** the top navigation includes a "Browse" or "Recipes" link pointing to `/browse`

#### Scenario: Mobile bottom nav includes Browse
- **WHEN** an authenticated user views the browse page on a mobile viewport
- **THEN** the bottom navigation includes an item linking to `/browse`

#### Scenario: My Recipes remains accessible
- **WHEN** an authenticated user wants to manage their own recipes
- **THEN** the existing `/recipes` route remains functional and accessible from the navigation
