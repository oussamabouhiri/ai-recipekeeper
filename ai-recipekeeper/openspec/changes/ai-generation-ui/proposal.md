## Why

The AI recipe generation backend is fully implemented (API endpoints, job queue, OpenRouter integration) but there's no UI for it. Users can't generate recipes via AI from the web interface. The app needs at minimum a form to submit generation requests and a page to track status/results.

## What Changes

- **Add** "Generate with AI" button to recipes index page
- **Create** `GenerationWebController` with `create`, `store`, `show` methods
- **Create** `generations/create.blade.php` — form for submitting ingredients and preferences
- **Create** `generations/show.blade.php` — status tracking with auto-refresh, shows recipe when complete
- **Add** web routes under `auth` middleware
- **Add** navigation link in navbar

## Capabilities

### New Capabilities

None — this is a UI-only change with no spec-level behavior changes.

### Modified Capabilities

None — existing API behavior remains unchanged.

## Impact

- **Files added**: `app/Http/Controllers/GenerationWebController.php`, `resources/views/generations/create.blade.php`, `resources/views/generations/show.blade.php`
- **Files modified**: `routes/web.php`, `resources/views/layouts/app.blade.php`, `resources/views/recipes/index.blade.php`
- **No API changes**: Uses existing `POST /api/generate` and `GET /api/generations/{id}` endpoints
- **No migrations**: Uses existing tables
