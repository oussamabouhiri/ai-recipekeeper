## Why

The current authentication pages (login/register) use Bootstrap 5 via CDN and display the default Laravel welcome page at `/`. The project's build system is configured for Tailwind CSS v4 via Vite, but no view currently loads those assets. This creates a visual inconsistency: the auth pages look like a generic Laravel scaffold rather than matching the project's intended design language. Additionally, visiting `/` shows the unmodified Laravel 13 welcome page, which is confusing for users and does not route them to login or dashboard.

## What Changes

- Replace Bootstrap-based login and register Blade templates with Tailwind CSS v4 designs matching the supplied authentication UI mockups
- Integrate Playfair Display, Inter, and Material Symbols fonts into the guest layout
- Add working password visibility toggles to login (1 field), register (2 fields)
- Move the authentication background image from `Images/Auth/image.png` to `public/images/auth/image.png` and use `asset()` helper
- Update `resources/views/layouts/guest.blade.php` to load Vite/Tailwind assets instead of Bootstrap CDN for auth pages only
- Update the root `/` route to redirect guests to `/login` and authenticated users to `/dashboard`
- Add authentication UI rendering tests and root redirect tests
- Preserve all existing Laravel authentication behavior (CSRF, validation, session, redirects)

## Capabilities

### New Capabilities
- `auth-ui`: Authentication UI presentation layer — login page, register page, guest layout, password visibility toggles, auth-specific styling

### Modified Capabilities
- `authentication`: Add requirement for root route redirect behavior (guest → /login, authenticated → /dashboard). Existing registration/login/logout requirements unchanged.

## Impact

### Files Modified
- `resources/views/auth/login.blade.php` — full UI redesign
- `resources/views/auth/register.blade.php` — full UI redesign
- `resources/views/layouts/guest.blade.php` — replace Bootstrap CDN with Vite/Tailwind for auth pages
- `routes/web.php` — update root `/` route
- `resources/css/app.css` — add auth-specific design tokens and font imports
- `resources/js/app.js` — add password visibility toggle logic

### Files Added
- `public/images/auth/image.png` — copied from `Images/Auth/image.png`
- `openspec/changes/auth-ui-redesign/specs/auth-ui/spec.md` — delta spec
- `openspec/changes/auth-ui-redesign/specs/authentication/spec.md` — delta spec

### Files Unchanged (Protected)
- `resources/views/dashboard.blade.php`
- `resources/views/recipes/**/*.blade.php`
- `resources/views/admin/**/*.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/admin.blade.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Http/Requests/Auth/RegisterRequest.php`
- `app/Models/User.php`
- All recipe, favorite, review, generation, category controllers and models
- All API routes and Sanctum configuration
- Database migrations and schema
- `vite.config.js` (no changes needed)
- `tailwind.config.js` (project uses Tailwind v4 CSS config, not JS)

### Dependencies
- No new Composer packages
- No new NPM packages (Tailwind v4 and Vite already configured)
- Fonts loaded via Google Fonts CDN (Playfair Display, Inter, Material Symbols)
