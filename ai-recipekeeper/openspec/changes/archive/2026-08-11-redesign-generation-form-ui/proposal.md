## Why

The AI Recipe Generator form page (`generations/create`) uses the legacy Bootstrap layout (`layouts.app`) while the rest of the application uses the modern Tailwind/Material Design 3 layout (`layouts.dashboard`). This creates an inconsistent user experience. The current form is a basic Bootstrap card with minimal visual polish, lacking the glassmorphism effects, elegant typography, and spacious layout present throughout the rest of the application. The redesign brings this critical page in line with the established design system.

## What Changes

- Switch the generation form from `layouts.app` (Bootstrap 5.3.3) to `layouts.dashboard` (Tailwind v4 + Material Design 3 tokens)
- Update `GenerationWebController::create()` to pass `$user` and `$initials` variables required by the dashboard layout
- Redesign the form UI with glassmorphism cards, a bento-style grid layout, and Material Design 3 components
- Add a sticky AI preview panel on the right side (desktop) with atmospheric background and "Ready to Cook?" messaging
- Replace the difficulty dropdown with segmented button UI (Easy | Medium | Hard)
- Replace the servings number input with a polished range slider with live value display
- Add a loading overlay/shimmer state when the form is submitted
- Add hero section with Playfair Display heading and descriptive subtitle
- Ensure responsive behavior: 2-column grid on desktop, single column stacked on mobile/tablet

## Capabilities

### New Capabilities

- `generation-form-ui`: The visual presentation and user experience of the AI recipe generation form, including layout structure, card styling, input components, preview panel, loading states, and responsive behavior

### Modified Capabilities

(None - no existing spec-level behavior changes. This is purely a UI/UX presentation change.)

## Impact

- **Files Modified**: `resources/views/generations/create.blade.php` (full UI rewrite), `app/Http/Controllers/GenerationWebController.php` (add `$user`/`$initials` to `create()`)
- **Layout System**: Switches from Bootstrap CDN to Tailwind v4 via Vite (already the project standard)
- **No Backend Changes**: All form field names, validation rules, CSRF handling, route endpoints, job dispatch, and redirect logic remain identical
- **No Database Changes**: No schema, model, or migration changes
- **No AI Logic Changes**: The `GenerateRecipeJob` and `OpenRouterService` are untouched
- **Dependencies**: Uses existing Tailwind v4 theme tokens from `resources/css/app.css`, Google Fonts (Playfair Display, Inter, Material Symbols), all already loaded by the dashboard layout
