## 1. Controller Update

- [x] 1.1 Update `GenerationWebController::create()` to compute `$user` and `$initials` using the same pattern as `DashboardController` (lines 39-43 of `DashboardController.php`)
- [x] 1.2 Pass `$user` and `$initials` to the view via `compact()`

## 2. View Layout Switch

- [x] 2.1 Change `@extends('layouts.app')` to `@extends('layouts.dashboard')` in `generations/create.blade.php`
- [x] 2.2 Update `@section('title')` value to match the new design

## 3. Hero Section

- [x] 3.1 Add hero section inside `@section('content')` with centered heading "Generate Your Next Masterpiece" using `font-display-lg text-display-lg text-on-surface`
- [x] 3.2 Add subtitle paragraph using `font-body-lg text-body-lg text-on-surface-variant`

## 4. Bento Grid Layout

- [x] 4.1 Create the outer grid container: `grid grid-cols-1 lg:grid-cols-12 gap-6`
- [x] 4.2 Create the left column (form): `lg:col-span-7 space-y-6`
- [x] 4.3 Create the right column (preview panel): `lg:col-span-5 relative`

## 5. Ingredients Card

- [x] 5.1 Wrap the ingredients section in a glassmorphism card: `bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl p-6`
- [x] 5.2 Add card header with `kitchen` Material Symbol icon and "Ingredients on Hand" heading using `font-headline-md text-headline-md text-primary`
- [x] 5.3 Add description text: "What's in your pantry or fridge?"
- [x] 5.4 Redesign ingredient rows from Bootstrap `input-group` to Tailwind flex layout: ingredient name input + quantity input + remove button with `close` Material Symbol icon
- [x] 5.5 Restyle the "Add Ingredient" button with `text-primary font-label-md` and `add` Material Symbol icon
- [x] 5.6 Preserve `@error` validation display with red text styling

## 6. Preferences Card

- [x] 6.1 Create a glassmorphism card for Preferences with `tune` Material Symbol icon
- [x] 6.2 Add textarea field with `old('preferences')` value preservation
- [x] 6.3 Style the textarea with Tailwind: `bg-surface-container-lowest border border-outline-variant rounded-lg px-4 py-2 focus:border-primary focus:ring-1 focus:ring-primary`

## 7. Constraints Card

- [x] 7.1 Create a glassmorphism card for Constraints with `schedule` Material Symbol icon (or appropriate icon)
- [x] 7.2 Add textarea field with `old('constraints')` value preservation
- [x] 7.3 Style the textarea consistently with the Preferences textarea

## 8. Servings Range Slider

- [x] 8.1 Replace the number input with `<input type="range" min="1" max="12" value="{{ old('servings', 4) }}" name="servings">`
- [x] 8.2 Add a `<span>` element to display the current slider value
- [x] 8.3 Add `oninput` JavaScript handler to update the displayed value when the slider moves

## 9. Difficulty Segmented Buttons

- [x] 9.1 Replace the `<select>` dropdown with three `<button>` elements: Easy, Medium, Hard
- [x] 9.2 Add a hidden `<input name="difficulty" type="hidden" id="difficulty" value="{{ old('difficulty') }}">` for form submission
- [x] 9.3 Add JavaScript click handlers to toggle the hidden input value and update button visual states
- [x] 9.4 Style the selected button with `bg-primary-container text-on-primary-container` and unselected with `border border-outline-variant text-on-surface-variant`

## 10. Generate Recipe Button

- [x] 10.1 Create a full-width primary CTA button: `w-full py-4 bg-primary text-on-primary rounded-xl font-headline-md text-headline-md`
- [x] 10.2 Add `auto_awesome` Material Symbol icon and "Generate Recipe" text
- [x] 10.3 Add shadow and hover/active transition effects

## 11. AI Preview Panel

- [x] 11.1 Create the sticky preview container: `sticky top-24 bg-surface/70 backdrop-blur-xl border border-outline-variant/30 rounded-xl overflow-hidden h-[600px] flex flex-col justify-center items-center text-center p-6`
- [x] 11.2 Add atmospheric background image at low opacity (20%) using the kitchen image URL from the reference design
- [x] 11.3 Add `restaurant_menu` Material Symbol icon in a primary-container circle
- [x] 11.4 Add "Ready to Cook?" heading and descriptive paragraph

## 12. Loading State

- [x] 12.1 Add a hidden loading overlay div with spinner, "Crafting Your Recipe..." text, and "Consulting the culinary models." subtitle
- [x] 12.2 Add JavaScript to show the overlay and disable the submit button on form `submit` event
- [x] 12.3 Style the overlay as a fixed-position element covering the form area with `bg-surface/90 backdrop-blur-sm`

## 13. JavaScript Preservation

- [x] 13.1 Preserve the existing ingredient add/remove JavaScript logic (lines 86-121 of current `create.blade.php`)
- [x] 13.2 Update the JS to target the new DOM structure (new class names for ingredient rows and remove buttons)
- [x] 13.3 Keep the JS inside `@section('scripts')` block

## 14. Responsive Behavior Verification

- [x] 14.1 Verify desktop layout: 2-column grid with sticky preview panel
- [x] 14.2 Verify tablet layout: single column with preview below form
- [x] 14.3 Verify mobile layout: single column, stacked elements, mobile bottom nav visible
- [x] 14.4 Verify active navigation highlighting on both desktop header and mobile bottom nav

## 15. Form Functionality Verification

- [x] 15.1 Verify form POSTs to `generations.store` with correct CSRF token
- [x] 15.2 Verify ingredient array field names (`ingredients[0][name]`, `ingredients[0][quantity]`) are preserved
- [x] 15.3 Verify validation error display with `@error` directives
- [x] 15.4 Verify `old()` input persistence on validation failure
- [x] 15.5 Verify servings and difficulty values submit correctly
- [x] 15.6 Verify redirect to `generations.show` after successful submission
