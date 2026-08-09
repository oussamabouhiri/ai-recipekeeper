## 1. Authorization

- [x] 1.1 Create `CategoryPolicy` with `create`, `update`, `delete` methods returning `$user->isAdmin()`
- [x] 1.2 Verify policy auto-discovery (or register via `Gate::policy`) and that non-admin create/update/delete returns 403

## 2. Validation

- [x] 2.1 Create `StoreCategoryRequest` with name required, string, max:255, unique:categories; description nullable string
- [x] 2.2 Create `UpdateCategoryRequest` with name required, string, max:255, unique:categories,name,{$category->id}; description nullable string

## 3. API Resource

- [x] 3.1 Create `CategoryResource` whitelisting id, name, description, created_at, updated_at with optional `recettes` relationship via `whenLoaded()`

## 4. Controller

- [x] 4.1 Create `CategoryController` with `index` (paginate all categories), `store` (authorize + create), `show` (find or fail), `update` (authorize + update), `destroy` (authorize + delete + 204)
- [x] 4.2 Add `use AuthorizesRequests` trait to `CategoryController`

## 5. Routes

- [x] 5.1 Register API routes in `routes/api.php`: public `GET /api/categories` and `GET /api/categories/{category}`; `auth:sanctum` `POST /api/categories`, `PUT /api/categories/{category}`, `DELETE /api/categories/{category}`
- [x] 5.2 Register web routes in `routes/web.php` under `auth` → `admin` middleware: `GET /admin/categories`, `GET /admin/categories/create`, `POST /admin/categories`, `GET /admin/categories/{category}/edit`, `PUT /admin/categories/{category}`, `DELETE /admin/categories/{category}`

## 6. Factory & Seeder

- [x] 6.1 Create `CategoryFactory` with faker name and nullable description
- [x] 6.2 Create `CategorySeeder` creating sample categories; register in `DatabaseSeeder`

## 7. Blade Views

- [x] 7.1 Create `resources/views/categories/index.blade.php` extending admin layout with category list table, create button, edit/delete actions
- [x] 7.2 Create `resources/views/categories/create.blade.php` with form for name and description fields, validation error display
- [x] 7.3 Create `resources/views/categories/edit.blade.php` with pre-filled form for name and description fields, validation error display
- [x] 7.4 Create `resources/views/layouts/admin.blade.php` if needed for consistent admin navigation (or extend existing admin layout)

## 8. Tests

- [x] 8.1 `CategoryCrudTest`: guest list returns all categories; guest show returns category; admin create 201; admin update 200; admin delete 204; cascade delete removes pivots but not recipes
- [x] 8.2 `CategoryAuthorizationTest`: non-admin create/update/delete returns 403; guest create/update/delete returns 401
- [x] 8.3 `CategoryValidationTest`: missing name → 422; duplicate name → 422; name too long → 422

## 9. Verification

- [x] 9.1 Run `php artisan test` and fix failures
- [x] 9.2 Run `vendor/bin/pint` to enforce code style
- [x] 9.3 Run `openspec validate` and confirm the change validates
