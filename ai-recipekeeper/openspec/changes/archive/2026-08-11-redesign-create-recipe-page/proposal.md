## Why

The Create Recipe page (`/recipes/create`) currently extends `layouts/app.blade.php` (Bootstrap 5.3 with a dark navbar), while every other page in the application uses `layouts/dashboard.blade.php` (Tailwind CSS + Material Design 3 tokens with Playfair Display/Inter typography). This creates a jarring visual disconnect — the create page looks like a different product from the rest of the app. The redesign brings the create page into visual alignment with the established dashboard design system.

## What Changes

- **Layout switch**: The create page template switches from `layouts/app.blade.php` (Bootstrap) to `layouts/dashboard.blade.php` (Tailwind + MD3), matching all other app pages
- **Two-column layout**: The single-column Bootstrap form becomes a two-column dashboard layout — left column (65-70%) for main content, right column (30-35%) for sidebar
- **Card-based sections**: Basic Details, Ingredients, and Preparation Steps become styled cards with rounded corners, subtle borders, soft shadows, and generous padding
- **Sidebar relocation**: Prep Time, Cook Time, and Servings move from the Basic Information card into a dedicated "Timing & Yield" sidebar card
- **Categories restyled**: The native multi-select `<select>` becomes a chip/pill-based tag display with an input for adding categories
- **Sticky sidebar**: The right sidebar (Timing & Yield, Categorization, Save/Cancel) remains sticky on desktop while scrolling
- **Mobile responsive**: Single-column layout on mobile with sidebar below main content
- **No backend changes**: All form field names, validation, CSRF, controller logic, and database interactions remain identical

## Capabilities

### New Capabilities

_None._

### Modified Capabilities

- `recipe-blade-ui`: The "Create recipe form" requirement's visual presentation changes. The form's fields, validation behavior, dynamic ingredient/step functionality, and submission logic remain unchanged. Only the layout, styling, and visual structure of the page are redesigned to match the Tailwind + MD3 dashboard design system.

## Impact

- **Files modified**: `resources/views/recipes/create.blade.php` (template rewrite from Bootstrap to Tailwind markup)
- **Files unchanged**: Controller, model, form request, routes, JavaScript for dynamic ingredients/steps, CSS tokens
- **No breaking changes**: Form field names, POST target, CSRF token, validation behavior, and redirect logic are preserved exactly
- **Design system alignment**: The create page becomes visually consistent with dashboard, browse, index, and favorites pages
