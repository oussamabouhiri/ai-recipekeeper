## Context

The project is a fresh Laravel 13 application (see proposal.md - Why) with the default users table, sessions table, and password_reset_tokens table, an untouched `routes/web.php`, no API routes, no Sanctum, and no auth controllers. The frontend is Blade + Bootstrap 5 with a custom, responsive design system required by the project rules. The existing spec-driven workflow mandates planning artifacts before code, and the user requested keeping the existing users table, Laravel User model, and MCD/MLD untouched.

## Goals / Non-Goals

**Goals:**
- Session-based web authentication (registration, login, logout) written by hand with Laravel's `Auth` facade and Blade views — no Breeze/Jetstream boilerplate
- Sanctum bearer tokens for future API authentication (minimal setup + one protected route group)
- Two-role model (`user`/`admin`) via a single `is_admin` flag on the existing users table
- Admin middleware enforcing admin-only routes, and a Policy foundation authorizing users to access only their own resources
- Form Requests for all authentication input validation

**Non-Goals:**
- Password reset/confirmation, email verification, social login, 2FA
- Recipe CRUD, AI generation, dashboards, moderation UI/applications
- API endpoints beyond the auth/token baseline (the client API surface is deferred)
- Docker, CI, deployment, API documentation
- Roles table, permissions table, or any new tables (database-schema spec modified via `is_admin` only)

## Decisions

### Decision: Boolean `is_admin` instead of a roles table or string enum
**Choice**: Add a single `is_admin` boolean column (default `false`), plus an `isAdmin()` helper on the User model.
**Rationale**: KISS — exactly two roles, no join tables, no seeders to centralize role names. Every account is trivially classified, and `isAdmin()` reads clearly in controllers, middleware, and Blade. The user explicitly forbade unnecessary tables and required keeping the existing users table. Laravel's convention (e.g. `Gate::before`/`$user->isAdmin()`) favors this pattern for small apps.
**Alternatives**: `roles` + `role_user` tables (rejected: over-engineered for two roles); `role` string/enum column (rejected: less type-safe/readable, same DDL cost as boolean).

### Decision: Hand-written auth controllers instead of Laravel Breeze/Jetstream
**Context**: The project is a training project with a strict Blade/Bootstrap design system and no npm UI framework. Breeze introduces a Tailwind/React stack that conflicts with the design rules, while Jetstream brings Livewire complexity.
**Rationale**: Breeze's auth flow (session-based, `Auth::attempt`, `Auth::login`, `Auth::logout`, named routes) is simple enough to implement directly with `RegisteredUserController`, `AuthenticatedSessionController`, and Form Requests — the exact Laravel conventions the design rules mandate.
**Alternatives**: Breeze (rejected: imports Tailwind/React/UI templates contrary to the project frontend constraints).

### Decision: Sanctum with `HasApiTokens` on the User model
**Context**: The user asked for "Sanctum for API bearer-token authentication" per the project documentation.
**Rationale**: `laravel/sanctum` is the Laravel-recommended token layer, pairs `HasApiTokens` with the existing User model, and its `auth:sanctum` middleware guards API routes without extra tables beyond its own `personal_access_tokens` migration.
**Alternatives**: Laravel Passport (rejected: OAuth2 overkill); API Guard custom driver (rejected: rebuilding what Sanctum ships).

### Decision: Middleware-based admin gates
**Context**: Two-role applications rarely need a full Gate abstraction for role checks alone.
**Choice**: `EnsureUserIsAdmin` middleware (alias `admin`) placed on admin route groups; `auth` middleware first, then `admin`.
**Rationale**: Follows Laravel's middleware contract, is trivially testable, and reads clearly in the route file. The 403/redirect behavior per the authorization spec ships here.
**Alternatives**: Gate::before (rejected: global implicit access is harder to reason about); role checks inline in controllers (rejected: DRY violation).

### Decision: Policy foundation pre-built for ownership & moderation
**Choice**: Ship `UserPolicy` explicitly registered, and an `app/Policies/` baseline pattern with ownership-branded methods (`update`/`delete` style `isOwner` checks) + an `App\Policies\ModelPolicy`-style admin allowance, ready for future `RecettePolicy`, `AvisPolicy`, etc.
**Rationale**: Laravel pre-registers `ModelPolicy` (the generic fallback) — explicitly declaring policies makes authorization visible, testable, and the spec's "users only access resources they are authorized to access" testable.
**Alternatives**: Rely on `Gate::can` with closures (rejected: models don't scale; training value of Policies is exactly this).

## Risks / Trade-offs

- **Sanctum versioning brand-new Laravel 13**: `laravel/sanctum` must be pinned to a version supporting Laravel 13 → Mitigation: let composer resolve the latest compatible release, [`vendor:publish --tag=sanctum-config`] then smoke-test `GET $guard/api` in tests.
- **Boolean admin flag vs future moderation roles**: if moderation later needs per-area capabilities, `is_admin` must migrate to a roles system → Mitigation: keep all role logic behind `isAdmin()`/Policies so the future change is contained to one column and one guard seam.
- **Public registration surface**: naive Form Requests could allow mass-assignment of `is_admin` → Mitigation: `is_admin` is never in `$fillable` and never in Form Request `validated()`; the registration flow hard-codes `is_admin = false`.
- **Hand-rolled Blade auth views**: inconsistent visual design with the rest of the app → Mitigation: reuse the project's Blade layout conventions and Bootstrap 5 components so the auth pages match the responsive design rules.

## Migration Plan

1. `composer require laravel/sanctum`; publish its config; add `config/sanctum.php` entry point
2. Migration for the `is_admin` column (defaults `false`; reversible)
3. Feature branch `feature/authentication-system-basic-auth` (one feature per branch) per GitFlow
4. Deploy/rollback: standard Laravel — rollback =  `php artisan migrate:rollback --step=1` reverts the column since it lives in a single reversible migration, no destructive changes.

## Open Questions

- Admin provisioning is fixed (AdminUserSeeder, see Decisions) — no open question here.
- **Email verification**: `email_verified_at` exists but is unused (`MustVerifyEmail` not implemented). Assumed out of scope; verification can be added later without spec changes.
- **Post-login redirect target**: defaults to `/` for now; a future dashboard can change it without touching the auth contract.
- **Sanctum token expiry**: not configured (defaults, no expiration); adding expiration later is a task-level detail, not a spec change.