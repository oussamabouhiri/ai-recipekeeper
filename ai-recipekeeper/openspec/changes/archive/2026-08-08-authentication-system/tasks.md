## 1. Sanctum Setup

- [x] 1.1 Install `laravel/sanctum` via Composer and publish its config and migration
- [x] 1.2 Add Sanctum's `personal_access_tokens` migration by running migrations
- [x] 1.3 Add `HasApiTokens` trait to the User model

## 2. Database Role Column

- [x] 2.1 Create migration adding `is_admin` boolean column (default `false`) to the users table
- [x] 2.2 Run migration and verify schema with `php artisan migrate:status`

## 3. User Model & Authorization Helpers

- [x] 3.1 Add `isAdmin()` helper method to the User model
- [x] 3.2 Keep `is_admin` out of the model's fillable attributes (never mass-assignable)

## 4. Registration

- [x] 4.1 Create `RegisterRequest` Form Request (name, unique email, password confirmation, min length)
- [x] 4.2 Create `RegisteredUserController` (stores user with `is_admin = false`, logs the user in, redirects to `/`)
- [x] 4.3 Create registration Blade view with Bootstrap 5 and validation error display

## 5. Login / Logout

- [x] 5.1 Create `LoginRequest` Form Request (email, password)
- [x] 5.2 Create `AuthenticatedSessionController` (store: attempt login, regenerate session; destroy: logout, invalidate session, redirect to login)
- [x] 5.3 Create login Blade view with Bootstrap 5 and validation error display
- [x] 5.4 Add guest/authenticated route groups in `routes/web.php` (named auth routes)

## 6. API Token Authentication

- [x] 6.1 Create `routes/api.php` with a Sanctum-protected route group and a token-issuing route for authenticated users

## 7. Admin Authorization

- [x] 7.1 Create `EnsureUserIsAdmin` middleware returning 403 for non-admin users (after auth)
- [x] 7.2 Register the `admin` middleware alias in `bootstrap/app.php`
- [x] 7.3 Add an admin-only route group demonstrating the middleware

## 8. Policy Foundation

- [x] 8.1 Create and register `UserPolicy` with ownership-based authorization (and admin allowance for moderation)
- [x] 8.2 Verify policies are auto-discovered or registered in `AuthServiceProvider`

## 9. Admin Seeder

- [x] 9.1 Create `AdminUserSeeder` reading `ADMIN_*` env values
- [x] 9.2 Add `ADMIN_*` placeholders to `.env.example`

## 10. Tests & Validation

- [x] 10.1 Feature tests: successful registration, duplicate email, failed login, successful login, logout
- [x] 10.2 Feature tests: admin middleware (admin allowed, user 403/redirect, guest redirected to login)
- [x] 10.3 Feature tests: Sanctum token issue + token-protected route access
- [x] 10.4 Run `php artisan test` and fix failures
- [x] 10.5 Run `php artisan migrate:rollback --step=1` and re-migrate to verify reversibility
- [x] 10.6 Run Pint (`vendor/bin/pint`) to enforce code style