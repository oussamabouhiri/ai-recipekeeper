## Why

The AI Recipe Keeper application currently has no functional authentication or authorization layer: the default Laravel users table and User model exist, but there is no registration, login, logout, API token authentication, or role distinction. Recipes, reviews, favorites, and AI generations are all user-owned, so a secure authentication and role-based authorization foundation is required before any of those features can be built.

## What Changes

- Add user registration, login, and logout for web (session-based) using Laravel's built-in authentication and Blade views
- Install and configure Laravel Sanctum for API bearer-token authentication, exposed via a minimal API auth route group
- Add an `is_admin` boolean flag to the existing users table to distinguish the `user` and `admin` roles (no new roles table, no rename of the users table)
- Enforce role-based authorization: admin-only middleware, admin accounts only created via deliberate admin/seed actions, never through public registration
- Add an authorization foundation (admin middleware + initial Policies) prepared for future recipe ownership and moderation rules
- Use Form Requests for validation and Policies for authorization where appropriate
- Keep the existing users table, Laravel User model, validated MCD/MLD, and existing database structure intact

## Capabilities

### New Capabilities

- `authentication`: User registration, web login/logout using the session guard, and trusted API access through Sanctum bearer tokens
- `authorization`: Role-based access control distinguishing `user` and `admin` roles, with an admin middleware and the Policy foundation for user-owned resources

### Modified Capabilities

- `database-schema`: The users table gains an `is_admin` boolean column to support the two-role model (no new tables)

## Impact

- New migration: add `is_admin` to the existing users table (non-destructive)
- Update `app/Models/User.php` (add `isAdmin()` helper, Sanctum `HasApiTokens` trait, role visible to authorization logic)
- New controllers and Form Requests: `Auth\RegisteredUserController`, `Auth\AuthenticatedSessionController`
- New Blade views for registration and login (Bootstrap 5, responsive, consistent with project design rules)
- New middleware: `EnsureUserIsAdmin` (registers as `admin`)
- New Policies: `UserPolicy` foundation for user-owned resource authorization
- New routes in `routes/web.php` (guest/authenticated groups) and `routes/api.php` (Sanctum-protected)
- New dependency: `laravel/sanctum` (+ `config/sanctum.php`)
- No changes to existing tables, recipes, AI generation, dashboards, or deployment tooling