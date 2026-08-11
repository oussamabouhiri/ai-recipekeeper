## Context

The Create Recipe page (`resources/views/recipes/create.blade.php`) currently extends `layouts/app.blade.php`, a Bootstrap 5.3 layout with a dark navbar. Every other user-facing page (dashboard, browse, index, favorites) extends `layouts/dashboard.blade.php`, which uses Tailwind CSS compiled via Vite with Material Design 3 color tokens, Playfair Display for headlines, and Inter for body text. The create page looks like a different product.

The existing form handles: title, description, prep_time, cook_time, servings, difficulty, statut, categories (multi-select from DB), ingredients (dynamic select + quantity + unit from DB), and steps (dynamic step_number + instruction). JavaScript uses `<template>` elements with `__index__` placeholders for dynamic add/remove. The controller (`RecipeWebController@store`) uses `StoreRecetteRequest` for validation and `syncRelations()` for pivot management. None of this changes.

## Goals / Non-Goals

**Goals:**
- Switch the create page to the dashboard layout for visual consistency
- Implement a two-column responsive layout matching the reference design
- Restyle categories from native multi-select to chip/pill display
- Preserve all existing form functionality, field names, validation, and submission behavior
- Make the page fully responsive (desktop two-column, mobile single-column)

**Non-Goals:**
- Adding image upload (requires backend changes, separate task)
- Adding a "meal type" field (requires migration, separate task)
- Adding a tag system (requires new model/migration, separate task)
- Changing the edit recipe page (separate task)
- Modifying any backend logic, controller, model, or validation rules
- Adding new JavaScript libraries or frameworks

## Decisions

### Decision 1: Rewrite template markup, not just swap layout

**Choice**: Rewrite the entire `create.blade.php` content from Bootstrap classes to Tailwind utility classes, rather than trying to make Bootstrap and Tailwind coexist.

**Rationale**: Bootstrap and Tailwind classes conflict (e.g., both define `col`, `row`, `mb-3`, `form-control`). Mixing them causes unpredictable styling. The dashboard layout already loads Tailwind via Vite. A clean rewrite ensures consistent styling.

**Alternatives considered**:
- *Add Tailwind alongside Bootstrap*: Rejected — dual-class conflicts, increased CSS payload, unpredictable behavior.
- *Create a separate CSS file for the create page*: Unnecessary complexity — Tailwind utilities handle everything.

### Decision 2: Keep form field names identical

**Choice**: All `name` attributes remain exactly as-is: `title`, `description`, `prep_time`, `cook_time`, `servings`, `difficulty`, `statut`, `categories[]`, `ingredients[N][ingredient_id]`, `ingredients[N][quantity]`, `ingredients[N][unit]`, `etapes[N][step_number]`, `etapes[N][instruction]`.

**Rationale**: The controller's `store()` method, `StoreRecetteRequest` validation, and `syncRelations()` depend on these exact field names. Changing them would require backend modifications, which is explicitly out of scope.

### Decision 3: Relocate time/servings fields to sidebar

**Choice**: Move `prep_time`, `cook_time`, and `servings` from the Basic Information card into a "Timing & Yield" sidebar card. Move `difficulty` and `statut` into the sidebar or keep in Basic Details.

**Rationale**: The reference design places timing/yield in the right sidebar. This is a pure HTML layout change — the field names and validation remain identical. The `name` attributes are unchanged regardless of where the `<input>` appears in the DOM.

### Decision 4: Restyle categories as chips with text input

**Choice**: Replace the native `<select multiple>` with a text input + chip display. On form submission, selected category IDs are submitted as `categories[]`. JavaScript manages the chip add/remove and hidden input state.

**Rationale**: The reference design shows tags as removable chips. Categories are the closest existing concept. The underlying data model (many-to-many with `recette_categorie` pivot) supports this — we just change the UI from multi-select to chip-based selection. The existing `$categories` collection passed from the controller provides the available options.

**Implementation approach**: A hidden `<select>` or set of hidden checkboxes maintains the actual `categories[]` form data. The visible chip UI is JavaScript-driven. When a user selects a category, a chip appears and a hidden input is added/removed.

### Decision 5: Adapt JavaScript templates for new markup

**Choice**: Update the `<template>` elements for ingredients and steps to use Tailwind classes and the new layout structure (numbered circles for steps, side-by-side amount/name for ingredients). Keep the same `__index__` replacement pattern and event delegation.

**Rationale**: The JavaScript pattern (clone template, replace `__index__`, append) is sound and minimal. Only the HTML inside the templates changes to match the new visual design. The `name` attribute patterns remain identical.

### Decision 6: Use the dashboard layout's existing navigation

**Choice**: The create page extends `layouts/dashboard.blade.php` which provides the top navigation bar, mobile bottom nav, and footer. No custom navigation is needed.

**Rationale**: The dashboard layout already handles authenticated navigation, mobile responsiveness, and the app's navigation structure. The create page just needs to pass `user` and `initials` variables to the layout (currently the controller's `create()` method doesn't pass these — they'll need to be added).

**Required controller change**: The `create()` method must pass `$user` and `$initials` to the view, matching how `index()`, `browse()`, and other dashboard pages do it. This is a minor addition, not a logic change.

## Risks / Trade-offs

- **[Controller variable binding]** → The `create()` method currently only passes `$categories` and `$ingredients`. The dashboard layout expects `$user` and `$initials`. Mitigation: Add 4 lines to `create()` to compute and pass these variables. This is a trivial addition with no behavioral impact.

- **[Category chip UX]** → Users unfamiliar with chip-based selection may not know how to add categories. Mitigation: Include a visible dropdown or autocomplete input with clear affordance. The reference design shows a text input with category suggestions.

- **[Mobile sidebar stacking]** → On mobile, the sidebar cards appear below the main content, making the page longer. Mitigation: This is standard responsive behavior and matches the reference design. The existing mobile bottom nav provides quick access to primary actions.

- **[Visual regression on edit page]** → The edit page (`edit.blade.php`) still uses the Bootstrap layout. Users may notice inconsistency between create and edit. Mitigation: Out of scope for this change. The edit page redesign can follow as a separate task.
