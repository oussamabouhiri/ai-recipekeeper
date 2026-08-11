## Context

The generation form page (`generations/create`) currently extends `layouts.app`, a legacy Bootstrap 5.3.3 layout loaded via CDN. The rest of the application (dashboard, recipes, favorites) uses `layouts.dashboard`, a modern Tailwind v4 layout with Material Design 3 tokens, Google Fonts (Playfair Display, Inter), Material Symbols icons, and a responsive desktop header + mobile bottom nav. The `GenerationWebController::create()` method currently returns the view with no data, but the dashboard layout requires `$user` and `$initials` variables.

## Goals / Non-Goals

**Goals:**
- Unify the generation form page with the existing Tailwind/Material Design 3 design system
- Create a polished, bento-style layout with glassmorphism cards
- Add a sticky AI preview panel on desktop
- Improve input components (segmented difficulty buttons, range slider for servings)
- Add a loading state overlay during form submission
- Maintain 100% backend compatibility (field names, validation, routes, CSRF, job dispatch)

**Non-Goals:**
- Changing backend validation rules, routes, or controllers beyond `create()` data passing
- Modifying the AI generation pipeline (`GenerateRecipeJob`, `OpenRouterService`)
- Changing the `generations/show` page or its polling mechanism
- Adding new database fields or migrations
- Implementing real-time AI streaming or WebSocket updates
- Changing the `layouts.dashboard` template itself

## Decisions

### Decision 1: Switch layout from `layouts.app` to `layouts.dashboard`

**Choice**: Use `layouts.dashboard` as-is.

**Rationale**: The dashboard layout already provides the complete design system (Tailwind v4, M3 tokens, Google Fonts, Material Symbols, responsive nav, footer). Switching to it is the most efficient path to visual consistency. The only prerequisite is passing `$user` and `$initials` from the controller.

**Alternatives considered**:
- Modifying `layouts.app` to match `layouts.dashboard` — rejected because it would affect other pages still using `layouts.app` (show page, potentially others) and duplicates effort.
- Creating a shared partial for nav/footer — rejected because the layout system is already established; following convention is better.

### Decision 2: Controller passes `$user` and `$initials` to the view

**Choice**: Compute `$user` and `$initials` in `GenerationWebController::create()` using the same pattern as `DashboardController`.

**Rationale**: The `layouts.dashboard` template references `$user->name` and `$initials` in the header. The `DashboardController` already computes these; replicating that exact pattern ensures consistency.

**Alternatives considered**:
- Using a View Composer to share `$user`/`$initials` globally — rejected because it would change the shared data model for all views using the dashboard layout, which is a larger scope change.
- Using `auth()->user()` directly in the layout — rejected because the layout currently expects `$user` as a variable, and changing the layout is out of scope.

### Decision 3: Bento grid layout (7/5 column split)

**Choice**: Use Tailwind's `grid grid-cols-1 lg:grid-cols-12` with `lg:col-span-7` for the form and `lg:col-span-5` for the preview panel.

**Rationale**: The 7/5 split gives the form adequate width for input fields while reserving enough space for the preview panel. On mobile, everything stacks vertically.

**Alternatives considered**:
- 8/4 split — rejected because the preview panel needs sufficient width for its content.
- Side-by-side without grid — rejected because CSS Grid provides cleaner responsive behavior.

### Decision 4: Glassmorphism card styling

**Choice**: Apply `bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl` to each card section.

**Rationale**: This follows the Material Design 3 glassmorphism pattern already established in the dashboard layout. Using Tailwind utility classes means no new CSS is needed.

**Alternatives considered**:
- Solid white cards with shadows — rejected because it doesn't match the modern glass effect used elsewhere.
- Adding a custom CSS class — rejected because Tailwind utilities can express this without new CSS.

### Decision 5: Segmented difficulty buttons with hidden input

**Choice**: Three `<button>` elements that toggle a hidden `<input name="difficulty">` value. Clicking a selected button deselects it (clears the hidden input).

**Rationale**: Segmented buttons are a Material Design 3 pattern. Using a hidden input ensures the value is submitted with the form without JavaScript form manipulation.

**Alternatives considered**:
- Keeping the `<select>` dropdown — rejected because it doesn't match the design direction.
- Using radio buttons styled as buttons — rejected because the hidden input approach is simpler and more accessible.

### Decision 6: Range slider for servings

**Choice**: `<input type="range" min="1" max="12">` with a JavaScript `oninput` handler updating a displayed `<span>`.

**Rationale**: Range sliders are more engaging than number inputs for bounded ranges. The `oninput` handler provides immediate visual feedback.

**Alternatives considered**:
- Keeping the number input — rejected because it's less visually polished.
- Using a custom slider component — rejected because the native range input is sufficient and avoids dependencies.

### Decision 7: Loading overlay via JavaScript on form submit

**Choice**: Listen to the form's `submit` event, show a fixed-position overlay with spinner and text, and disable the submit button.

**Rationale**: This provides immediate visual feedback without changing the backend redirect flow. The overlay appears briefly before the browser navigates to the show page.

**Alternatives considered**:
- AJAX form submission with loading state — rejected because it would require changing the controller to return JSON or handle AJAX, which is outside scope.
- CSS-only animation on the button — rejected because a full overlay provides better UX feedback.

### Decision 8: Reuse existing Tailwind theme tokens

**Choice**: Use only colors, fonts, and spacing already defined in `resources/css/app.css` (`@theme` block).

**Rationale**: No new CSS or configuration is needed. All M3 tokens (primary, surface, outline-variant, etc.) and font families (font-headline-md, font-body-md, etc.) are already available.

## Risks / Trade-offs

- **Risk**: The `layouts.dashboard` layout's `$user`/`$initials` requirement could break if the controller doesn't pass them → **Mitigation**: The controller change is minimal and follows the exact pattern from `DashboardController`.

- **Risk**: The loading overlay may flash briefly on fast submissions → **Mitigation**: Acceptable; the overlay provides feedback even for fast loads.

- **Risk**: The range slider max of 12 may not cover all use cases (current backend allows 1-100) → **Mitigation**: The backend validation still allows 1-100; the slider is a UI convenience. Users who need more can potentially use the URL or API.

- **Risk**: The preview panel's background image (external URL) may be slow to load or unavailable → **Mitigation**: The image is decorative only at low opacity; text remains readable without it.

## Migration Plan

This is a presentation-only change with no database migrations or deployment complexity:

1. Deploy the updated `GenerationWebController.php` and `generations/create.blade.php` together
2. No environment variables, configuration, or queue changes needed
3. Rollback: revert both files to their previous versions

## Open Questions

None. All design decisions are resolved based on the existing codebase patterns.
