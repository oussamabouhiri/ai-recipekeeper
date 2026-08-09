## 1. Authenticated Layout

- [x] 1.1 Create `resources/views/layouts/app.blade.php` with Bootstrap 5 CDN, responsive navbar (brand, nav links for Dashboard/Recipes, user name, logout form), and `@yield('content')` / `@yield('scripts')` sections
- [x] 1.2 Update `routes/web.php` to add a `/dashboard` route that renders a simple dashboard view using the new layout

## 2. Recipe Web Controller & Routes

- [x] 2.1 Create `app/Http/Controllers/RecipeWebController.php` with `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` methods
- [x] 2.2 Add recipe web routes to `routes/web.php` under the `auth` middleware group: `GET /recipes`, `GET /recipes/create`, `POST /recipes`, `GET /recipes/{recipe}`, `GET /recipes/{recipe}/edit`, `PUT /recipes/{recipe}`, `DELETE /recipes/{recipe}`
- [x] 2.3 Implement `index` method: query recipes using `visibleTo` scope with eager loading, return `recipes.index` view
- [x] 2.4 Implement `show` method: apply `visibleTo` scope to ensure visibility rules, return `recipes.show` view or 404
- [x] 2.5 Implement `store` method: validate with `StoreRecetteRequest`, create recipe with `user_id` from auth, sync relations (etapes, ingredients, categories), redirect to show with success message
- [x] 2.6 Implement `edit` method: authorize via `RecettePolicy@update`, return `recipes.edit` view with pre-filled data
- [x] 2.7 Implement `update` method: authorize via `RecettePolicy@update`, validate with `UpdateRecetteRequest`, update recipe and relations, redirect to show with success message
- [x] 2.8 Implement `destroy` method: authorize via `RecettePolicy@delete`, delete recipe, redirect to index with success message

## 3. Recipe List View

- [x] 3.1 Create `resources/views/recipes/index.blade.php` extending `layouts.app`
- [x] 3.2 Display a "My Recipes" heading with a "Create Recipe" button
- [x] 3.3 Render recipe cards/table rows showing title, status badge (published/hidden), and action buttons (View, Edit, Delete)
- [x] 3.4 Show empty state message with link to create recipe when no recipes exist
- [x] 3.5 Add delete form with CSRF and method spoofing inside a confirmation modal

## 4. Recipe Detail View

- [x] 4.1 Create `resources/views/recipes/show.blade.php` extending `layouts.app`
- [x] 4.2 Display recipe title, description, prep_time, cook_time, servings, difficulty, and status
- [x] 4.3 Display categories as badges or a list
- [x] 4.4 Display ingredients in a table with name, quantity, and unit columns
- [x] 4.5 Display steps ordered by step_number with instruction text
- [x] 4.6 Show Edit and Delete buttons conditionally based on policy (owner or admin)
- [x] 4.7 Add delete form with CSRF and method spoofing inside a confirmation modal

## 5. Create Recipe Form

- [x] 5.1 Create `resources/views/recipes/create.blade.php` extending `layouts.app`
- [x] 5.2 Build form fields: title (required), description (textarea), prep_time, cook_time, servings, difficulty (select or text), status (select: published/hidden)
- [x] 5.3 Build categories multi-select dropdown populated from `Category::all()`
- [x] 5.4 Build dynamic ingredients section: container with template row containing ingredient dropdown, quantity input, unit input, and remove button; "Add ingredient" button to clone template
- [x] 5.5 Build dynamic steps section: container with template row containing step_number input, instruction textarea, and remove button; "Add step" button to clone template
- [x] 5.6 Display Laravel validation errors next to corresponding fields using `@error` directives
- [x] 5.7 Implement vanilla JavaScript for dynamic add/remove of ingredient and step entries
- [x] 5.8 Ensure form POSTs to the store route with correct array naming for nested fields (etapes[0][step_number], ingredients[0][ingredient_id], etc.)

## 6. Edit Recipe Form

- [x] 6.1 Create `resources/views/recipes/edit.blade.php` extending `layouts.app`
- [x] 6.2 Reuse the same form structure as create, pre-filled with existing recipe data
- [x] 6.3 Pre-select existing categories in the multi-select
- [x] 6.4 Pre-populate dynamic ingredient entries with existing ingredient relationships (ingredient_id, quantity, unit)
- [x] 6.5 Pre-populate dynamic step entries with existing etapes (step_number, instruction)
- [x] 6.6 Ensure form uses PUT method spoofing and posts to the update route
- [x] 6.7 Display validation errors on update failure

## 7. Delete Confirmation

- [x] 7.1 Implement Bootstrap 5 modal for delete confirmation on list and detail pages
- [x] 7.2 Modal contains warning text and Confirm/Cancel buttons
- [x] 7.3 Confirm button submits a form with DELETE method and CSRF token
- [x] 7.4 Cancel button closes the modal without action

## 8. Feature Tests

- [x] 8.1 Create `tests/Feature/RecipeBladeTest.php` with `RefreshDatabase`
- [x] 8.2 Test guest is redirected to login when accessing any `/recipes` route
- [x] 8.3 Test authenticated user can see recipe list with own recipes and published recipes
- [x] 8.4 Test authenticated user can view a published recipe detail page
- [x] 8.5 Test owner can view their own hidden recipe detail page
- [x] 8.6 Test non-owner gets 404 when viewing another user's hidden recipe
- [x] 8.7 Test authenticated user can access the create recipe form
- [x] 8.8 Test successful recipe creation redirects to detail page with success message
- [x] 8.9 Test validation errors are displayed on invalid create submission
- [x] 8.10 Test owner can access the edit form for their own recipe
- [x] 8.11 Test non-owner gets 403 when accessing edit form for another user's recipe
- [x] 8.12 Test successful recipe update redirects to detail page
- [x] 8.13 Test owner can delete their own recipe
- [x] 8.14 Test non-owner gets 403 when attempting to delete another user's recipe
- [x] 8.15 Test admin can view, edit, and delete any recipe
