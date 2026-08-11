## 1. Controller Update

- [x] 1.1 Add `$user` and `$initials` computation to `RecipeWebController@create()` method, matching the pattern used in `index()` and `browse()`

## 2. Template Layout Switch

- [x] 2.1 Change `create.blade.php` from `@extends('layouts.app')` to `@extends('layouts.dashboard')`
- [x] 2.2 Rewrite the page header section with "Create a New Recipe" title, subtitle, and Cancel/Save buttons using Tailwind + MD3 classes
- [x] 2.3 Create the two-column grid structure: left column (`lg:col-span-8`) for main content, right column (`lg:col-span-4`) for sticky sidebar

## 3. Basic Details Card

- [x] 3.1 Build the Basic Details card with white surface, rounded corners, subtle border, soft shadow, and comfortable padding
- [x] 3.2 Rewrite the title input with Tailwind styling, proper label, required indicator, and error state support
- [x] 3.3 Rewrite the description textarea with Tailwind styling and error state support
- [x] 3.4 Add a cover image upload/dropzone visual (UI only — connects to existing `image_path` field as a text input since no upload backend exists)

## 4. Ingredients Card

- [x] 4.1 Build the Ingredients card with header and icon
- [x] 4.2 Rewrite the `<template id="ingredient-template">` with new layout: amount select (25% width) + ingredient name select (flex-grow) + remove button, using Tailwind classes
- [x] 4.3 Preserve all `name` attribute patterns: `ingredients[__index__][ingredient_id]`, `ingredients[__index__][quantity]`, `ingredients[__index__][unit]`
- [x] 4.4 Style the "Add Ingredient" button as a full-width dashed border button with icon

## 5. Preparation Steps Card

- [x] 5.1 Build the Preparation Steps card with header and icon
- [x] 5.2 Rewrite the `<template id="step-template">` with numbered circle (green bg, white text) + textarea + remove/reorder buttons, using Tailwind classes
- [x] 5.3 Preserve all `name` attribute patterns: `etapes[__index__][step_number]`, `etapes[__index__][instruction]`
- [x] 5.4 Style the "Add Step" button as a full-width dashed border button with icon

## 6. Right Sidebar — Timing & Yield

- [x] 6.1 Build the Timing & Yield card with header and border-bottom divider
- [x] 6.2 Place prep_time and cook_time inputs side-by-side in a 2-column grid with "min" suffix
- [x] 6.3 Place servings input below in full width
- [x] 6.4 Preserve all `name` attributes: `prep_time`, `cook_time`, `servings`

## 7. Right Sidebar — Categorization

- [x] 7.1 Build the Categorization card with header and border-bottom divider
- [x] 7.2 Rewrite the categories multi-select as a chip/pill display with a dropdown or autocomplete input for selection
- [x] 7.3 Implement JavaScript for chip add/remove: clicking a category adds a visible chip + hidden input, clicking chip X removes both
- [x] 7.4 Preserve the `categories[]` form field name for submission
- [x] 7.5 Style selected categories as rounded pills with primary-container background and remove icon

## 8. Right Sidebar — Sticky Save/Cancel

- [x] 8.1 Add sticky Save Recipe and Cancel buttons in the sidebar (visible on desktop only via `hidden lg:flex`)
- [x] 8.2 Style Save Recipe as primary green button with icon, Cancel as outlined secondary button
- [x] 8.3 Ensure the form's submit button triggers the existing POST to `route('recipes.store')`

## 9. JavaScript Adaptation

- [x] 9.1 Update the ingredient add/remove JavaScript to work with the new template markup and Tailwind classes
- [x] 9.2 Update the step add/remove JavaScript to work with the new template markup and numbered circles
- [x] 9.3 Implement category chip JavaScript: selection from dropdown adds chip + hidden input, chip remove button removes both
- [x] 9.4 Ensure step numbers auto-update when steps are added or removed (if not already handled by the template pattern)

## 10. Responsive Design

- [x] 10.1 Verify desktop layout: two-column with sticky sidebar at >= 1024px
- [x] 10.2 Verify tablet layout: sidebar moves below main content at 768px-1023px
- [x] 10.3 Verify mobile layout: single column, all sections stacked, no horizontal scrolling at < 768px
- [x] 10.4 Verify mobile bottom navigation is visible and functional

## 11. Validation & Error States

- [x] 11.1 Style validation error messages within the new Tailwind design system (red text, proper spacing)
- [x] 11.2 Ensure `@error` directives work correctly with the new form field names
- [x] 11.3 Test form submission with missing required fields (title) and verify errors display properly

## 12. Visual QA & Testing

- [x] 12.1 Test full recipe creation flow: fill all fields, add ingredients, add steps, select categories, submit
- [x] 12.2 Test dynamic ingredient add/remove
- [x] 12.3 Test dynamic step add/remove
- [x] 12.4 Test category chip add/remove
- [x] 12.5 Test validation error display
- [x] 12.6 Test Cancel button navigates to recipes index
- [x] 12.7 Compare final page visually against the reference design and fix spacing, typography, sizing, alignment issues
- [x] 12.8 Test on mobile viewport for responsive layout
