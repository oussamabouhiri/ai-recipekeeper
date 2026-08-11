## MODIFIED Requirements

### Requirement: Favorite listing
The system SHALL list the authenticated user's own favorites with their recipes, supporting server-side search and category filtering, and SHALL NOT reveal other users' favorites.

#### Scenario: User lists own favorites
- **WHEN** an authenticated user requests the favorite list
- **THEN** the system returns only the favorites created by that user
- **AND** each favorite includes its favorited recipe with its categories and reviews

#### Scenario: Guest listing rejected
- **WHEN** an unauthenticated visitor requests the favorite list
- **THEN** the system rejects the request with a 401 response

#### Scenario: No favorites yet
- **WHEN** an authenticated user with no favorites requests the favorite list
- **THEN** the system returns an empty collection

#### Scenario: Search by title
- **WHEN** an authenticated user submits a search term via the `search` query parameter
- **THEN** the system returns only favorites whose recipe title contains the search term (case-insensitive LIKE)

#### Scenario: Search by description
- **WHEN** an authenticated user submits a search term that matches no recipe titles
- **THEN** the system returns favorites whose recipe description contains the search term

#### Scenario: Empty search returns all favorites
- **WHEN** an authenticated user submits the search form with an empty search field
- **THEN** the system returns all favorites as if no search was performed

#### Scenario: Filter by category
- **WHEN** an authenticated user submits a category ID via the `category` query parameter
- **THEN** the system returns only favorites whose recipe belongs to the specified category

#### Scenario: Category filter scoped to user's favorites
- **WHEN** the category filter chips are displayed
- **THEN** the system shows only categories that exist in the authenticated user's favorited recipes, ordered by name

#### Scenario: "All" clears category filter
- **WHEN** an authenticated user requests the favorite list without a `category` parameter
- **THEN** the system returns all favorites regardless of category

#### Scenario: Search and category combine
- **WHEN** an authenticated user provides both a `search` term and a `category` ID
- **THEN** the system returns only favorites matching both the search term and the category

#### Scenario: Search and filter preserve through pagination
- **WHEN** an authenticated user navigates to page 2 of filtered results
- **THEN** the URL retains the search and category parameters and results remain filtered

### Requirement: Favorite Blade UI
The system SHALL provide a Tailwind/MD3 Blade interface at `GET /favorites` for authenticated users to browse and manage their favorites using recipe cards in a responsive grid, with search and category filtering.

#### Scenario: My Favorites page
- **WHEN** an authenticated user navigates to `GET /favorites`
- **THEN** the system renders a page displaying the user's favorited recipes as recipe cards in a responsive grid

#### Scenario: Favorites page uses dashboard layout
- **WHEN** the favorites page renders
- **THEN** the page uses the `layouts/dashboard.blade.php` layout with the modern Tailwind/MD3 design system, desktop top navigation, and mobile bottom navigation

#### Scenario: Favorites page header
- **WHEN** the favorites page renders
- **THEN** the system displays a heading (e.g., "Curated Favorites") with a supporting description

#### Scenario: Search input
- **WHEN** the favorites page renders
- **THEN** the system displays a search input field that submits as a GET form to the favorites index route

#### Scenario: Category filter chips
- **WHEN** the favorites page renders
- **THEN** the system displays a horizontal row of category chips: "All" (always first), followed by categories present in the user's favorites ordered by name

#### Scenario: Active category highlighted
- **WHEN** a category filter is active
- **THEN** the corresponding chip is visually distinguished from inactive chips

#### Scenario: Recipe card displays image
- **WHEN** a favorited recipe has a valid `image_path` and the file exists on disk
- **THEN** the card displays the recipe image with the recipe title as alt text

#### Scenario: Recipe card shows fallback when no image
- **WHEN** a favorited recipe's `image_path` is null or the referenced file does not exist
- **THEN** the card displays a Material Symbols restaurant icon as a placeholder

#### Scenario: Recipe card shows heart/remove button
- **WHEN** a recipe card renders for an authenticated user's favorite
- **THEN** the card displays a filled heart icon button that submits a DELETE form to remove the favorite

#### Scenario: Heart button does not trigger navigation
- **WHEN** an authenticated user clicks the heart/remove button on a recipe card
- **THEN** the system removes the favorite via the existing server-side endpoint without navigating to the recipe detail page

#### Scenario: Recipe card shows category badges
- **WHEN** a favorited recipe has categories
- **THEN** the card displays category badge(s) for the recipe

#### Scenario: Recipe card shows title and description
- **WHEN** a recipe card renders
- **THEN** the card displays the recipe title (line-clamped to 2 lines) and description (line-clamped to 2 lines)

#### Scenario: Recipe card shows time and servings metadata
- **WHEN** a recipe card renders
- **THEN** the card displays prep time, cook time, and servings with Material Symbols icons, omitting any field that is null

#### Scenario: Recipe card shows difficulty
- **WHEN** a favorited recipe has a difficulty value
- **THEN** the card displays the difficulty as metadata

#### Scenario: Recipe card shows real rating
- **WHEN** a favorited recipe has one or more reviews in the `avis` table
- **THEN** the card displays the real average rating and real review count derived from those reviews

#### Scenario: Recipe card shows no rating when no reviews
- **WHEN** a favorited recipe has no reviews
- **THEN** the card does not display a rating or review count

#### Scenario: Recipe card links to detail page
- **WHEN** a user clicks a recipe card (not the heart button)
- **THEN** the system navigates to the existing `GET /favorites/{recipe}` route for that recipe

#### Scenario: Favorites page empty state
- **WHEN** an authenticated user with no favorites navigates to `GET /favorites`
- **THEN** the system displays an empty state with a bookmark icon, "No favorites yet" heading, helpful copy, and an "Explore Recipes" CTA button

#### Scenario: Empty state CTA links to browse
- **WHEN** the user clicks the "Explore Recipes" CTA in the empty state
- **THEN** the system navigates to the existing `GET /browse` route

#### Scenario: Search returns no results
- **WHEN** a search term matches no favorite recipes
- **THEN** the system displays an empty state indicating no favorites were found, with an option to clear filters

#### Scenario: Remove favorite from My Favorites page
- **WHEN** an authenticated user submits the remove action on the My Favorites page
- **THEN** the system deletes the favorite and returns the user to the My Favorites page

#### Scenario: Guest web access rejected
- **WHEN** a guest visits the favorites web page
- **THEN** the system redirects the guest to the login page

### Requirement: Responsive favorites grid
The system SHALL display recipe cards in a responsive grid that adapts to viewport width.

#### Scenario: Mobile viewport
- **WHEN** the favorites page is viewed on a viewport narrower than 640px
- **THEN** the recipe grid displays 1 card per row

#### Scenario: Tablet viewport
- **WHEN** the favorites page is viewed on a viewport between 640px and 1024px
- **THEN** the recipe grid displays 2 cards per row

#### Scenario: Desktop viewport
- **WHEN** the favorites page is viewed on a viewport wider than 1024px
- **THEN** the recipe grid displays 3 cards per row

### Requirement: Favorites page pagination
The system SHALL paginate favorites results using Laravel's built-in pagination, showing 12 favorites per page, with pagination links preserving search and filter parameters.

#### Scenario: Pagination displays
- **WHEN** more than 12 favorites match the current filters
- **THEN** the system displays pagination links below the recipe grid

#### Scenario: Default page size
- **WHEN** the favorites page loads
- **THEN** the system displays up to 12 favorites on the first page

#### Scenario: Pagination preserves query parameters
- **WHEN** a user clicks a pagination link while search or category filters are active
- **THEN** the URL retains the search and category parameters and results remain filtered

### Requirement: Favorites navigation active state
The system SHALL visually indicate the Favorites page as the active page in both desktop and mobile navigation.

#### Scenario: Desktop nav active state
- **WHEN** an authenticated user is on the favorites page
- **THEN** the "My Favorites" link in the desktop top navigation is visually distinguished as active

#### Scenario: Mobile nav active state
- **WHEN** an authenticated user is on the favorites page
- **THEN** the "Favs" item in the mobile bottom navigation is visually distinguished as active
