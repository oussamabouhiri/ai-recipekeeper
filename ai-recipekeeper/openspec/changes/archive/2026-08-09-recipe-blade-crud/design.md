## Context

The AI Recipe Keeper has a fully functional backend for recipe CRUD (KAN-13) exposed through an API at `/api/recipes`. The frontend currently has only guest auth pages (login, register) using a Bootstrap 5 layout. There is no authenticated app layout, no dashboard, and no Blade views for recipes. The project uses Blade templating, Bootstrap 5 via CDN, and standard Laravel patterns (Resource Controllers, Form Requests, Route Model Binding).

## Goals / Non-Goals

**Goals:**
- Provide a clean, responsive Blade UI for full recipe CRUD
- Reuse the existing backend logic (validation, authorization, visibility) without modifying the API
- Create a consistent authenticated layout that other features can extend
- Support dynamic form sections for ingredients and steps using vanilla JavaScript
- Keep the architecture simple and idiomatic to Laravel

**Non-Goals:**
- Modifying the API or backend behavior
- Adding AI recipe generation UI
- Implementing category/ingredient management UI
- Introducing React, Vue, or any frontend framework
- Adding image upload (image_path remains a string field)
- Modifying the database schema

## Decisions

### 1. Dedicated web controller instead of reusing RecetteController

**Decision:** Create a new `RecipeWebController` that handles web routes separately from the API controller.

**Rationale:** The existing `RecetteController` is designed for JSON responses (returns `RecetteResource`, uses `AuthorizesRequests` trait for API). A web controller needs to return views, handle form validation with redirects, and work with session-based auth. Mixing both concerns in one controller violates SRP and would require conditionals throughout.

**Alternatives considered:**
- Modifying `RecetteController` to handle both: Rejected because it would add conditional logic and break the clean API contract.
- Using a single resourceful controller: Not feasible since the API controller uses `RecetteResource` for responses.

### 2. Reuse existing Form Requests for validation

**Decision:** The `RecipeWebController` will use `StoreRecetteRequest` and `UpdateRecetteRequest` for validation, same as the API.

**Rationale:** This ensures consistent validation rules across API and web. The Form Requests already handle all recipe fields including nested etapes, ingredients, and categories.

**Trade-off:** The web controller must handle the `etapes`, `ingredients`, and `categories` array formatting the same way the API does (delete+recreate for etapes, sync for ingredients/categories).

### 3. Bootstrap 5 via CDN for consistency

**Decision:** Use Bootstrap 5 via CDN (matching the existing guest layout) rather than introducing Vite/TailwindCSS for recipe pages.

**Rationale:** The guest layout already uses Bootstrap 5.3.3 via CDN. The welcome page uses TailwindCSS via Vite but that is an isolated page. Consistency across authenticated pages is more important than switching CSS frameworks. The Vite + TailwindCSS setup in `vite.config.js` is only configured for the welcome page.

### 4. Vanilla JavaScript for dynamic form sections

**Decision:** Use vanilla JavaScript (no jQuery, no Alpine.js) for dynamically adding/removing ingredient and step entries.

**Rationale:** Keeps dependencies minimal. The dynamic behavior is straightforward (clone a template row, append to container, remove on click). No framework is needed for this level of interactivity.

### 5. Layout structure

**Decision:** Create `layouts/app.blade.php` as the authenticated layout with:
- Responsive Bootstrap 5 navbar with brand, nav links (Dashboard, Recipes), user name, and logout
- Main content area with `@yield('content')`
- Optional `@yield('scripts')` section for page-specific JavaScript

**Rationale:** Follows the same Blade inheritance pattern as the existing `layouts/guest.blade.php`. The `@yield` pattern is the standard Laravel approach.

### 6. Route model binding with visibility scope

**Decision:** Web routes will use Route Model Binding for `{recipe}`, but the controller will apply the `visibleTo` scope to ensure guests cannot access hidden recipes and non-owners cannot access others' hidden recipes.

**Rationale:** The `show` action in the API uses `visibleTo` scope on the query. The web controller must replicate this behavior. Route Model Binding fetches the model, then the controller applies the scope to verify visibility.

### 7. Delete confirmation with modal

**Decision:** Use a Bootstrap 5 modal for delete confirmation rather than a separate confirmation page.

**Rationale:** Reduces navigation friction. A modal with "Are you sure?" and Confirm/Cancel buttons is the standard UX pattern for destructive actions. The form inside the modal sends a DELETE request with CSRF token.

### 8. Redirect after create/update

**Decision:** After successful create or update, redirect to the recipe detail page (`recipes.show`) with a flash success message.

**Rationale:** Standard Laravel PRG (Post/Redirect/Get) pattern. Shows the user the result of their action and prevents form resubmission.

## Risks / Trade-offs

- **Duplicate authorization logic**: The web controller must check authorization (owner/admin) separately from the API. Mitigated by reusing `RecettePolicy` via `$this->authorize()`.
- **Form array formatting**: The `etapes` and `ingredients` arrays must match the API's expected format. Mitigated by careful Blade form naming and JavaScript for dynamic sections.
- **No image upload**: The `image_path` field is a string. Users cannot upload images through the UI. This is a known limitation documented as out of scope.
- **CDN dependency**: Bootstrap 5 is loaded from CDN. If CDN is unavailable, styling breaks. Mitigated by the existing project already using this pattern.

## Migration Plan

No migration needed. This change adds new files only:
- `resources/views/layouts/app.blade.php`
- `resources/views/recipes/index.blade.php`
- `resources/views/recipes/show.blade.php`
- `resources/views/recipes/create.blade.php`
- `resources/views/recipes/edit.blade.php`
- `app/Http/Controllers/RecipeWebController.php`
- `tests/Feature/RecipeBladeTest.php`

Modified files:
- `routes/web.php` (add recipe web routes)

Rollback: Remove the new files and revert `routes/web.php`.

## Open Questions

None. The requirements and approach are well-defined by the existing backend and project conventions.
