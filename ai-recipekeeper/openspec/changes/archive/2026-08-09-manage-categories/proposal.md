## Why

Categories are a core data entity in AI Recipe Keeper — the database schema, Eloquent model, and pivot relationships already exist — but there is no way to create, edit, or delete categories independently. Currently, categories can only be attached to recipes via the recipe CRUD payload, but no dedicated management interface exists. Without it, the application relies on database seeding or direct manipulation to populate the category list, which is unsustainable for production use.

## What Changes

- Add a `CategoryController` exposing category CRUD under the admin middleware (web routes) and a public/authenticated API for listing.
- Add `CategoryPolicy` enforcing admin-only authorization for create, update, and delete operations.
- Add `StoreCategoryRequest` and `UpdateCategoryRequest` Form Requests with validation.
- Add `CategoryResource` for API responses.
- Add `CategoryFactory` and `CategorySeeder` for development and tests.
- Add Blade views for admin category management (index, create, edit) following existing UI patterns.
- Add Feature tests covering CRUD, authorization, validation, and cascade behavior.
- Deleting a category that is attached to recipes removes the pivot association (cascade) — this is the existing database behavior and requires no additional logic.
- **Out of scope**: AI functionality, recipe CRUD modifications, category ownership/user association, category ordering/sorting, category hierarchy/nesting.

## Capabilities

### New Capabilities
- `category-management`: Category CRUD API and admin Blade views, authorization rules (admin-only for mutations), validation, and cascade behavior.

### Modified Capabilities
- `authorization`: Add `CategoryPolicy` to the existing policy foundation (admin-only for create/update/delete, no ownership check needed since categories are global).

## Impact

- **Code**: `app/Http/Controllers/CategoryController.php` (new), `app/Policies/CategoryPolicy.php` (new), `app/Http/Requests/StoreCategoryRequest.php` + `UpdateCategoryRequest.php` (new), `app/Http/Resources/CategoryResource.php` (new), `database/factories/CategoryFactory.php` (new), `database/seeders/CategorySeeder.php` (new), `resources/views/categories/` (new Blade views), `routes/web.php` (admin category routes), `routes/api.php` (category listing endpoint).
- **Database**: no new migrations (categories table and pivot already exist).
- **API**: new `GET /api/categories` (public/authenticated), `POST /api/categories` (admin), `GET /api/categories/{category}` (public/authenticated), `PUT /api/categories/{category}` (admin), `DELETE /api/categories/{category}` (admin).
- **Blade**: new `categories/index.blade.php`, `categories/create.blade.php`, `categories/edit.blade.php` under admin area.
- **Tests**: `tests/Feature/CategoryCrudTest.php`, `tests/Feature/CategoryAuthorizationTest.php`, `tests/Feature/CategoryValidationTest.php`.
