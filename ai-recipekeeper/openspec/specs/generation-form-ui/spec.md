# generation-form-ui Specification

## Purpose

Defines the visual presentation and interactive behavior of the AI recipe generation form page, ensuring it integrates with the modern Tailwind/Material Design 3 design system while preserving all existing backend functionality.

## Requirements

### Requirement: Page layout uses the dashboard design system
The generation form page SHALL use the `layouts.dashboard` layout template, which provides the desktop header navigation, mobile bottom navigation bar, and footer consistent with the rest of the application.

#### Scenario: Page renders with dashboard layout
- **WHEN** an authenticated user navigates to `GET /generations/create`
- **THEN** the page renders within the dashboard layout with desktop header nav, mobile bottom nav, and footer

#### Scenario: Active navigation highlighting
- **WHEN** the user is on the generation form page
- **THEN** the "Generate with AI" link in the desktop header nav and the "AI Gen" tab in the mobile bottom nav SHALL be visually highlighted as the active page

### Requirement: Hero section displays above the form
The page SHALL display a centered hero section above the form with a large heading "Generate Your Next Masterpiece" using Playfair Display typography and a subtitle describing the AI recipe generation capability.

#### Scenario: Hero section renders
- **WHEN** the generation form page loads
- **THEN** a centered heading "Generate Your Next Masterpiece" is displayed in Playfair Display font
- **AND** a subtitle "Let AI craft the perfect recipe based on what you have, what you crave, and how much time you have." is displayed below the heading

### Requirement: Two-column grid layout on desktop
On desktop viewports (lg breakpoint and above), the form SHALL occupy a 7-column width on the left, and an AI preview panel SHALL occupy a 5-column width on the right. On tablet and mobile, the layout SHALL stack vertically with the form first and preview panel below.

#### Scenario: Desktop layout
- **WHEN** the page is viewed on a viewport wider than the lg breakpoint (1024px+)
- **THEN** the form and preview panel are displayed side by side in a 7/5 column grid

#### Scenario: Mobile/tablet layout
- **WHEN** the page is viewed on a viewport narrower than the lg breakpoint
- **THEN** the form and preview panel are stacked vertically with the form on top

### Requirement: Ingredients card with glassmorphism styling
The ingredients section SHALL be displayed in a glassmorphism-styled card (semi-transparent background with blur effect and subtle border). Each ingredient row SHALL display the ingredient name input, quantity input, and a remove button. An "Add Ingredient" button SHALL be below the ingredient list.

#### Scenario: Ingredient rows display correctly
- **WHEN** the form loads
- **THEN** at least one ingredient row is displayed with name, quantity fields and a remove button

#### Scenario: Add ingredient dynamically
- **WHEN** the user clicks "Add Ingredient"
- **THEN** a new ingredient row is appended to the list
- **AND** the new row contains empty name and quantity inputs and a remove button

#### Scenario: Remove ingredient dynamically
- **WHEN** the user clicks the remove button on an ingredient row
- **AND** there is more than one ingredient row present
- **THEN** that ingredient row is removed from the DOM

#### Scenario: Cannot remove last ingredient
- **WHEN** only one ingredient row exists
- **THEN** the remove button on that row SHALL be hidden

### Requirement: Preferences card
The preferences section SHALL be displayed in a glassmorphism-styled card containing a textarea field for user preferences (cuisine style, dietary preferences, etc.).

#### Scenario: Preferences field renders
- **WHEN** the form loads
- **THEN** a textarea labeled "Preferences" is displayed with placeholder text
- **AND** previously entered values are preserved via old() helper on validation failure

### Requirement: Constraints card
The constraints section SHALL be displayed in a glassmorphism-styled card containing a textarea field for dietary or ingredient constraints.

#### Scenario: Constraints field renders
- **WHEN** the form loads
- **THEN** a textarea labeled "Constraints" is displayed with placeholder text
- **AND** previously entered values are preserved via old() helper on validation failure

### Requirement: Servings range slider
The servings field SHALL use a range slider input with a minimum of 1, maximum of 12, and a visible numeric display showing the current selected value. The default value SHALL be 4.

#### Scenario: Servings slider renders
- **WHEN** the form loads
- **THEN** a range slider is displayed for servings with min=1, max=12
- **AND** the current value "4" is displayed next to the slider

#### Scenario: Servings value updates
- **WHEN** the user moves the range slider
- **THEN** the displayed numeric value updates to match the slider position

### Requirement: Difficulty segmented buttons
The difficulty field SHALL use a segmented button group with three options: Easy, Medium, and Hard. Only one option can be selected at a time. A hidden input stores the selected value for form submission.

#### Scenario: Difficulty buttons render
- **WHEN** the form loads
- **THEN** three buttons labeled "Easy", "Medium", and "Hard" are displayed
- **AND** no button is selected by default (Any/no difficulty)

#### Scenario: Select difficulty
- **WHEN** the user clicks one of the difficulty buttons
- **THEN** that button becomes visually highlighted (primary container color)
- **AND** the hidden input value is updated to match
- **AND** any previously selected button is deselected

#### Scenario: Deselect difficulty
- **WHEN** the user clicks the already-selected difficulty button
- **THEN** the button is deselected
- **AND** the hidden input value is cleared

### Requirement: Generate Recipe submit button
The form SHALL display a full-width primary CTA button labeled "Generate Recipe" with a Material Symbol icon (`auto_awesome`). The button SHALL use the primary green color and be visually prominent.

#### Scenario: Generate button renders
- **WHEN** the form loads
- **THEN** a full-width button labeled "Generate Recipe" with an auto_awesome icon is displayed

#### Scenario: Form submission
- **WHEN** the user clicks "Generate Recipe"
- **THEN** the form POSTs to the `generations.store` route with all form data
- **AND** the user is redirected to the generation show page on success
- **AND** validation errors are displayed inline if present

### Requirement: AI preview panel
A sticky preview panel SHALL be displayed on the right side (desktop only) with a glassmorphism card containing an atmospheric kitchen background image at low opacity, a restaurant_menu icon, the heading "Ready to Cook?", and descriptive text explaining the AI generation process.

#### Scenario: Preview panel renders on desktop
- **WHEN** the page is viewed on a desktop viewport
- **THEN** a preview panel is displayed on the right side, sticky to the viewport
- **AND** the panel contains a background image at low opacity
- **AND** the panel displays the restaurant_menu icon, "Ready to Cook?" heading, and descriptive text

#### Scenario: Preview panel hidden on mobile
- **WHEN** the page is viewed on a mobile or tablet viewport
- **THEN** the preview panel is displayed below the form in a non-sticky layout

### Requirement: Loading state on form submission
When the form is submitted, a loading overlay SHALL appear covering the form area with a spinner animation and "Crafting Your Recipe..." text. The submit button SHALL be disabled during submission to prevent duplicate requests.

#### Scenario: Loading overlay appears
- **WHEN** the user clicks "Generate Recipe" and the form begins submitting
- **THEN** a loading overlay with spinner and "Crafting Your Recipe..." text appears over the form

#### Scenario: Submit button disabled during submission
- **WHEN** the form is submitting
- **THEN** the "Generate Recipe" button is disabled and visually indicates a loading state

### Requirement: Validation error display
Form validation errors SHALL be displayed inline below each relevant field using red text styling. The `@error` Blade directive SHALL be used for each field that may have validation errors.

#### Scenario: Validation errors shown
- **WHEN** the form is submitted with invalid data (e.g., no ingredients)
- **THEN** validation error messages are displayed below the relevant fields
- **AND** previously entered form values are preserved (old input)

### Requirement: Controller passes required layout data
The `GenerationWebController::create()` method SHALL pass `$user` and `$initials` variables to the view, which are required by the `layouts.dashboard` template for rendering the header navigation and user avatar.

#### Scenario: Controller provides user data
- **WHEN** the `create()` method is called
- **THEN** the view receives `$user` (authenticated user model) and `$initials` (user name initials string)
